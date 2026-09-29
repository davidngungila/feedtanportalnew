<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\SwfEntry;
use Illuminate\Http\Request;

class SwfController extends Controller
{
    public const TYPES = ['contribution', 'deduction', 'payout', 'claim'];

    private function filtered(Request $request, ?string $forceType = null)
    {
        $type = $forceType ?? $request->get('type', 'all');
        $q = trim((string) $request->get('q', ''));

        $query = SwfEntry::query()->with('member')->latest();

        if (in_array($type, self::TYPES, true)) {
            $query->where('type', $type);
        }
        if ($q !== '') {
            $query->where(fn ($w) => $w->where('receipt_no', 'like', "%{$q}%")
                ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$q}%")));
        }

        return [$query->paginate(15)->withQueryString(), $type, $q];
    }

    private function totals(): array
    {
        return [
            'totalIn' => (float) SwfEntry::where('type', 'contribution')->sum('amount'),
            'totalDeductions' => (float) SwfEntry::where('type', 'deduction')->sum('amount'),
            'totalPayouts' => (float) SwfEntry::where('type', 'payout')->sum('amount'),
            'totalClaims' => (float) SwfEntry::where('type', 'claim')->sum('amount'),
        ];
    }

    public function index(Request $request)
    {
        [$entries, $type, $q] = $this->filtered($request);

        return view('swf.index', ['entries' => $entries, 'type' => $type, 'q' => $q, ...$this->totals()]);
    }

    public function contributions(Request $request)
    {
        [$entries, $type, $q] = $this->filtered($request, 'contribution');

        return view('swf.type', ['entries' => $entries, 'type' => 'contribution', 'q' => $q, 'title' => 'SWF Contributions', ...$this->totals()]);
    }

    public function deductions(Request $request)
    {
        [$entries, $type, $q] = $this->filtered($request, 'deduction');

        return view('swf.type', ['entries' => $entries, 'type' => 'deduction', 'q' => $q, 'title' => 'SWF Deductions', ...$this->totals()]);
    }

    public function claims(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = SwfEntry::query()->with('member')->whereIn('type', ['claim', 'payout'])->latest();

        if ($q !== '') {
            $query->where(fn ($w) => $w->where('receipt_no', 'like', "%{$q}%")
                ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$q}%")));
        }

        $entries = $query->paginate(15)->withQueryString();

        return view('swf.type', ['entries' => $entries, 'type' => 'claim', 'q' => $q, 'title' => 'SWF Claims', ...$this->totals()]);
    }

    public function accounts(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = Member::query()->latest();

        if ($q !== '') {
            $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"));
        }

        $members = $query->paginate(15)->withQueryString();

        return view('swf.accounts', ['members' => $members, 'q' => $q, ...$this->totals()]);
    }

    public function statements(Request $request)
    {
        $members = Member::orderBy('name')->get();
        $memberId = null;
        if ($request->filled('member_id')) {
            try {
                $memberId = did($request->get('member_id'));
            } catch (\Throwable) {
                $memberId = null;
            }
        }
        $member = $memberId ? Member::with('swfEntries')->find($memberId) : null;
        $entries = $member ? $member->swfEntries()->latest()->paginate(20) : null;

        return view('swf.statements', compact('members', 'member', 'entries', 'memberId'));
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();

        return view('swf.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'type' => ['required', 'in:contribution,deduction,payout,claim'],
            'amount' => ['required', 'numeric', 'min:100'],
            'method' => ['required', 'in:cash,mobile,bank'],
            'transacted_at' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $entry = \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            return SwfEntry::create([
                ...$data,
                'receipt_no' => 'SWF-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'received_by' => auth()->id(),
            ]);
        });

        return match ($data['type']) {
            'contribution' => redirect()->route('swf.contributions')->with('status', 'Contribution recorded and posted to the ledger.'),
            'deduction' => redirect()->route('swf.deductions')->with('status', 'Deduction recorded and posted to the ledger.'),
            default => redirect()->route('swf.claims')->with('status', 'Claim recorded and posted to the ledger.'),
        };
    }

    public function edit(SwfEntry $entry)
    {
        return view('swf.edit', compact('entry'));
    }

    public function update(Request $request, SwfEntry $entry)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $entry->update($data);

        return redirect()->route('swf.index')->with('status', 'SWF entry updated.');
    }

    public function destroy(SwfEntry $entry)
    {
        $entry->delete();

        return back()->with('status', 'SWF entry removed (journal reversed).');
    }
}
