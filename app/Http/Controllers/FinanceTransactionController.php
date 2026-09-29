<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\Member;
use App\Services\FinancePosting;
use Illuminate\Http\Request;

class FinanceTransactionController extends Controller
{
    private function applyFilters($query, array $filter, string $from, string $to, string $q)
    {
        foreach ($filter as $col => $val) {
            if (is_array($val)) {
                $query->whereIn($col, $val);
            } else {
                $query->where($col, $val);
            }
        }
        if ($from !== '') {
            $query->whereDate('transacted_at', '>=', $from);
        }
        if ($to !== '') {
            $query->whereDate('transacted_at', '<=', $to);
        }
        if ($q !== '') {
            $query->where(fn ($w) => $w->where('reference', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$q}%")));
        }

        return $query;
    }

    private function list(Request $request, string $view, string $title, array $filter = [])
    {
        $q = trim((string) $request->get('q', ''));
        $from = $request->get('from', '');
        $to = $request->get('to', '');

        $transactions = $this->applyFilters(
            FinanceTransaction::query()->with(['account', 'member'])->latest(),
            $filter, $from, $to, $q
        )->paginate(15)->withQueryString();

        $total = (float) $this->applyFilters(
            FinanceTransaction::query(), $filter, $from, $to, $q
        )->sum('amount');

        return view($view, compact('transactions', 'q', 'from', 'to', 'title', 'total'));
    }

    public function transactions(Request $request)
    {
        return $this->list($request, 'finance.transactions', 'Transactions');
    }

    public function income(Request $request)
    {
        return $this->list($request, 'finance.transactions', 'Income', ['type' => 'income']);
    }

    public function expenses(Request $request)
    {
        return $this->list($request, 'finance.transactions', 'Expenses', ['type' => 'expense']);
    }

    public function fees(Request $request)
    {
        return $this->list($request, 'finance.transactions', 'Fees & Charges', ['category' => ['fee', 'charge']]);
    }

    public function interest(Request $request)
    {
        return $this->list($request, 'finance.transactions', 'Interest', ['category' => 'interest']);
    }

    public function commissions(Request $request)
    {
        return $this->list($request, 'finance.transactions', 'Commissions', ['category' => 'commission']);
    }

    public function adjustments(Request $request)
    {
        return $this->list($request, 'finance.transactions', 'Adjustments', ['category' => 'adjustment']);
    }

    public function create(Request $request)
    {
        $accounts = FinanceAccount::where('type', 'asset')->where('status', 'active')->orderBy('code')->get();
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $preset = $request->get('category', 'other');
        $presetType = $request->get('type', 'income');

        return view('finance.transaction-create', compact('accounts', 'members', 'preset', 'presetType'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'category' => ['required', 'in:'.implode(',', FinanceTransaction::CATEGORIES)],
            'finance_account_id' => ['required', 'exists:finance_accounts,id'],
            'member_id' => ['nullable', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:100'],
            'transacted_at' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        FinancePosting::assertOpenPeriod($data['transacted_at']);

        $tx = FinanceTransaction::create([
            ...$data,
            'reference' => FinancePosting::reference('FT'),
            'created_by' => auth()->id(),
        ]);

        FinancePosting::postTransaction($tx->fresh());

        return redirect()->route('finance.transactions')->with('status', 'Entry recorded and posted to the ledger.');
    }

    public function destroy(FinanceTransaction $transaction)
    {
        if ($transaction->journal) {
            $transaction->journal->lines()->delete();
            $transaction->journal->delete();
        }
        $transaction->delete();

        return back()->with('status', 'Entry removed (journal reversed).');
    }
}
