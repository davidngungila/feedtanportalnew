<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentProduct;
use App\Models\Member;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $q = trim((string) $request->get('q', ''));

        $query = Investment::query()->with(['member', 'product'])->latest();

        if (in_array($status, ['active', 'matured', 'withdrawn'], true)) {
            $query->where('status', $status);
        }
        if ($q !== '') {
            $query->where(fn ($w) => $w->where('investment_no', 'like', "%{$q}%")
                ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$q}%")));
        }

        $investments = $query->paginate(15)->withQueryString();

        return view('investments.index', [
            'investments' => $investments, 'status' => $status, 'q' => $q,
            'totalActive' => (float) Investment::where('status', 'active')->sum('amount'),
        ]);
    }

    public function active(Request $request)
    {
        $request->merge(['status' => 'active']);

        return $this->index($request);
    }

    public function matured(Request $request)
    {
        $request->merge(['status' => 'matured']);

        return $this->index($request);
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $products = InvestmentProduct::where('status', 'active')->orderBy('name')->get();

        return view('investments.create', compact('members', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'investment_product_id' => ['nullable', 'exists:investment_products,id'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'expected_return_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'start_date' => ['required', 'date'],
            'maturity_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'plan' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,matured,withdrawn'],
            'notes' => ['nullable', 'string'],
        ]);

        $investment = \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            return Investment::create([
                ...$data,
                'investment_no' => 'INV-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'expected_return' => round($data['amount'] * $data['expected_return_rate'] / 100, 2),
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('investments.index')->with('status', 'Investment recorded and posted to the ledger.');
    }

    public function show(Investment $investment)
    {
        $investment->load(['member', 'product', 'returns', 'payouts.member']);
        $returnIds = $investment->returns->pluck('id');
        $payoutIds = $investment->payouts->pluck('id');
        $journals = \App\Models\JournalEntry::where(function ($q) use ($investment, $returnIds, $payoutIds) {
            $q->where(fn ($w) => $w->where('source_type', \App\Models\Investment::class)->where('source_id', $investment->id))
                ->orWhere(fn ($w) => $w->where('source_type', \App\Models\InvestmentReturn::class)->whereIn('source_id', $returnIds))
                ->orWhere(fn ($w) => $w->where('source_type', \App\Models\InvestmentPayout::class)->whereIn('source_id', $payoutIds));
        })->latest('entry_date')->get();

        return view('investments.show', compact('investment', 'journals'));
    }

    public function member(\App\Models\Member $member)
    {
        $member->load(['investments.product', 'investments.returns', 'payouts' => fn ($q) => $q->latest()]);

        $active = $member->investments->where('status', 'active');
        $matured = $member->investments->where('status', 'matured');

        return view('investments.member', [
            'member' => $member,
            'activeTotal' => $active->sum('amount'),
            'maturedTotal' => $matured->sum('amount'),
            'expectedTotal' => $member->investments->sum('expected_return'),
            'paidReturns' => $member->investments->flatMap->returns->sum('amount'),
            'pendingPayouts' => $member->payouts->whereIn('status', ['pending', 'verified'])->sum('net_cash'),
        ]);
    }

    public function edit(Investment $investment)
    {
        return view('investments.edit', compact('investment'));
    }

    public function update(Request $request, Investment $investment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,matured,withdrawn'],
            'maturity_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($investment, $data) {
            $investment->update($data);
        });

        return redirect()->route('investments.show', $investment)->with('status', 'Investment updated.');
    }

    public function destroy(Investment $investment)
    {
        $investment->delete();

        return redirect()->route('investments.index')->with('status', 'Investment removed (journals reversed).');
    }
}
