<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberApplication;
use App\Models\MemberGroup;
use App\Models\MemberType;
use Illuminate\Http\Request;

class MemberApplicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = MemberApplication::query()->with(['memberType', 'memberGroup'])->where('status', '!=', 'draft')->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('member-applications.index', compact('applications', 'status'));
    }

    public function create()
    {
        $types = MemberType::where('status', 'active')->orderBy('name')->get();
        $groups = MemberGroup::where('status', 'active')->orderBy('name')->get();

        return view('member-applications.create', compact('types', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'national_id' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:255'],
            'member_type_id' => ['nullable', 'exists:member_types,id'],
            'member_group_id' => ['nullable', 'exists:member_groups,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['status'] = 'pending';
        MemberApplication::create($data);

        return redirect()->route('member-applications.index')->with('status', 'Application submitted.');
    }

    public function show(MemberApplication $memberApplication)
    {
        $memberApplication->load(['memberType', 'memberGroup']);

        return view('member-applications.show', ['application' => $memberApplication]);
    }

    public function update(Request $request, MemberApplication $memberApplication)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $memberApplication->update([...$data, 'reviewed_by' => auth()->id()]);

        return redirect()->route('member-applications.index')->with('status', 'Application updated.');
    }

    public function approve(MemberApplication $memberApplication)
    {
        $member = \Illuminate\Support\Facades\DB::transaction(function () use ($memberApplication) {
            $member = Member::create([
                'member_no' => 'M-'.now()->format('Ymd').'-'.str_pad((string) (Member::max('id') + 1), 4, '0', STR_PAD_LEFT),
                'member_type_id' => $memberApplication->member_type_id,
                'name' => $memberApplication->name,
                'phone' => $memberApplication->phone,
                'email' => $memberApplication->email,
                'national_id' => $memberApplication->national_id,
                'address' => $memberApplication->address,
                'join_date' => now()->toDateString(),
                'status' => 'active',
            'notes' => $memberApplication->notes,
            'created_by' => auth()->id(),
        ]);
        $extra = array_filter([
            $memberApplication->referrer ? 'Introduced by: '.$memberApplication->referrer : null,
            $memberApplication->bank_name ? 'Bank: '.$memberApplication->bank_name.' '.($memberApplication->bank_account ?? '') : null,
        ]);
        if ($extra) {
            $member->update(['notes' => trim(($member->notes ? $member->notes."\n" : '').implode("\n", $extra))]);
        }

            if ($memberApplication->member_group_id) {
                $member->groups()->sync([$memberApplication->member_group_id]);
            }

            // Link the applicant's login (if they signed up) and unlock services.
            $loginUser = $memberApplication->user_id
                ? \App\Models\User::find($memberApplication->user_id)
                : ($memberApplication->email ? \App\Models\User::where('email', $memberApplication->email)->first() : null);

            if ($loginUser) {
                $loginUser->update(['member_id' => $member->id]);
                $slugs = array_values(array_unique([...array_diff($loginUser->roleSlugs(), ['applicant']), 'member']));
                $loginUser->roles()->sync(\App\Models\Role::whereIn('slug', $slugs)->pluck('id'));
                $staff = array_values(array_intersect($slugs, ['administrator', 'admin', 'chairperson', 'secretary', 'accountant', 'loan_officer', 'deposit_officer', 'investment_officer', 'swf_officer']));
                $loginUser->update(['role' => $staff[0] ?? 'member']);
            }

            $memberApplication->update(['status' => 'approved', 'reviewed_by' => auth()->id()]);

            return $member;
        });

        return redirect()->route('members.show', $member)->with('status', 'Application approved — member created and login unlocked.');
    }

    public function destroy(MemberApplication $memberApplication)
    {
        $memberApplication->delete();

        return redirect()->route('member-applications.index')->with('status', 'Application removed.');
    }
}
