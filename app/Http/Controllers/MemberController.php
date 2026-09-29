<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberGroup;
use App\Models\MemberType;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status', 'all');
        $typeId = $request->get('type_id', 'all');

        $query = Member::query()->with(['memberType'])->withCount('loans')->latest();

        if ($q !== '') {
            $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")
                ->orWhere('member_no', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%"));
        }
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }
        if ($typeId !== 'all' && is_numeric($typeId)) {
            $query->where('member_type_id', (int) $typeId);
        }

        $members = $query->paginate(15)->withQueryString();
        $types = MemberType::orderBy('name')->get();

        return view('members.index', compact('members', 'q', 'status', 'types', 'typeId'));
    }

    public function create()
    {
        $types = MemberType::where('status', 'active')->orderBy('name')->get();
        $groups = MemberGroup::where('status', 'active')->orderBy('name')->get();

        return view('members.create', compact('types', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:members,phone'],
            'email' => ['nullable', 'email'],
            'national_id' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:255'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
            'member_type_id' => ['nullable', 'exists:member_types,id'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['exists:member_groups,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $groups = $data['groups'] ?? [];
        unset($data['groups']);

        $data['member_no'] = 'M-'.now()->format('Ymd').'-'.str_pad((string) (Member::max('id') + 1), 4, '0', STR_PAD_LEFT);
        $data['join_date'] ??= now()->toDateString();
        $data['created_by'] = auth()->id();

        $member = Member::create($data);
        $member->groups()->sync($groups);

        return redirect()->route('members.show', $member)->with('status', 'Member registered.');
    }

    public function show(Member $member)
    {
        $member->load(['loans.repayments', 'deposits', 'investments', 'swfEntries', 'memberType', 'groups', 'documents']);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $types = MemberType::orderBy('name')->get();
        $groups = MemberGroup::where('status', 'active')->orderBy('name')->get();
        $member->load('groups');

        return view('members.edit', compact('member', 'types', 'groups'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:members,phone,'.$member->id],
            'email' => ['nullable', 'email'],
            'national_id' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:255'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
            'member_type_id' => ['nullable', 'exists:member_types,id'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['exists:member_groups,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $groups = $data['groups'] ?? null;
        unset($data['groups']);

        $member->update($data);
        if (is_array($groups)) {
            $member->groups()->sync($groups);
        }

        return redirect()->route('members.show', $member)->with('status', 'Member updated.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('members.index')->with('status', 'Member removed.');
    }
}
