<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\Loan;
use App\Models\Deposit;
use App\Models\Investment;
use App\Models\Receivable;
use App\Models\SwfEntry;
use Illuminate\Http\Request;

class FinanceStatementController extends Controller
{
    public function hub()
    {
        return view('finance.statements');
    }

    public function trialBalance(Request $request)
    {
        $asOf = $request->get('as_of', now()->toDateString());

        $rows = FinanceAccount::orderBy('code')->get()->map(function ($a) use ($asOf) {
            $dr = $a->postedDebit(null, $asOf) + (in_array($a->type, ['asset', 'expense'], true) ? (float) $a->opening_balance : 0);
            $cr = $a->postedCredit(null, $asOf) + (in_array($a->type, ['asset', 'expense'], true) ? 0 : (float) $a->opening_balance);

            return ['account' => $a, 'debit' => $dr - min($dr, $cr), 'credit' => $cr - min($dr, $cr)];
        })->filter(fn ($r) => abs($r['debit']) > 0.004 || abs($r['credit']) > 0.004)->values();

        return view('finance.trial-balance', [
            'asOf' => $asOf,
            'rows' => $rows,
            'totalDebit' => $rows->sum('debit'),
            'totalCredit' => $rows->sum('credit'),
        ]);
    }

    public function income(Request $request)
    {
        $from = $request->get('from', now()->startOfYear()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $revenues = FinanceAccount::where('type', 'income')->where('status', 'active')->orderBy('code')->get()
            ->map(fn ($a) => ['account' => $a, 'amount' => $a->balance($from, $to) - (float) $a->opening_balance])
            ->filter(fn ($r) => abs($r['amount']) > 0.004);

        $expenses = FinanceAccount::where('type', 'expense')->where('status', 'active')->orderBy('code')->get()
            ->map(fn ($a) => ['account' => $a, 'amount' => $a->balance($from, $to) - (float) $a->opening_balance])
            ->filter(fn ($r) => abs($r['amount']) > 0.004);

        $totalRevenue = $revenues->sum('amount');
        $totalExpenses = $expenses->sum('amount');

        return view('finance.income-statement', compact('from', 'to', 'revenues', 'expenses', 'totalRevenue', 'totalExpenses'));
    }

    public function balance(Request $request)
    {
        $asOf = $request->get('as_of', now()->toDateString());

        $assets = FinanceAccount::where('type', 'asset')->where('status', 'active')->orderBy('code')->get()
            ->map(fn ($a) => ['account' => $a, 'amount' => $a->balance(null, $asOf)]);
        $liabilities = FinanceAccount::where('type', 'liability')->where('status', 'active')->orderBy('code')->get()
            ->map(fn ($a) => ['account' => $a, 'amount' => $a->balance(null, $asOf)]);
        $equity = FinanceAccount::where('type', 'equity')->where('status', 'active')->orderBy('code')->get()
            ->map(fn ($a) => ['account' => $a, 'amount' => $a->balance(null, $asOf)]);

        // Member funds (from modules) — read-only memo section.
        $loanOut = max(0, (float) Loan::whereIn('status', ['active', 'overdue', 'paid'])->sum('total_payable') - (float) \App\Models\LoanRepayment::sum('amount'));
        $savingsNet = (float) Deposit::where('type', 'deposit')->sum('amount') - (float) Deposit::where('type', 'withdrawal')->sum('amount');
        $swfNet = (float) SwfEntry::where('type', 'contribution')->sum('amount') - (float) SwfEntry::whereIn('type', ['payout', 'claim', 'deduction'])->sum('amount');
        $invested = (float) Investment::whereIn('status', ['active', 'matured'])->sum('amount');

        $totalAssets = $assets->sum('amount') + $loanOut + $invested;
        $totalLiab = $liabilities->sum('amount') + $savingsNet + $swfNet;
        $totalEquity = $equity->sum('amount');

        return view('finance.balance-sheet', compact(
            'asOf', 'assets', 'liabilities', 'equity',
            'loanOut', 'savingsNet', 'swfNet', 'invested',
            'totalAssets', 'totalLiab', 'totalEquity'
        ));
    }

    public function cashflowStatement(Request $request)
    {
        $from = $request->get('from', now()->startOfYear()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $txns = FinanceTransaction::whereBetween('transacted_at', [$from, $to])->get();
        $receipts = $txns->where('type', 'income')->groupBy('category')->map->sum('amount');
        $payments = $txns->where('type', 'expense')->groupBy('category')->map->sum('amount');

        $cashIds = \App\Services\FinancePosting::cashAccountIds();
        $closing = FinanceAccount::whereIn('id', $cashIds)->get()->sum(fn ($a) => $a->balance(null, $to));
        $net = $receipts->sum() - $payments->sum();
        $opening = $closing - $net;

        return view('finance.cashflow-statement', compact('from', 'to', 'receipts', 'payments', 'opening', 'closing', 'net'));
    }

    public function reports(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $byCategory = FinanceTransaction::whereBetween('transacted_at', [$from, $to])->get()
            ->groupBy(fn ($t) => $t->type.'.'.$t->category)
            ->map(fn ($g) => ['type' => $g->first()->type, 'category' => $g->first()->categoryLabel(), 'count' => $g->count(), 'total' => $g->sum('amount')])
            ->sortBy('type')->values();

        $receivableOut = (float) Receivable::where('kind', 'receivable')->whereIn('status', ['open', 'partial', 'overdue'])->get()->sum->outstanding();
        $payableOut = (float) Receivable::where('kind', 'payable')->whereIn('status', ['open', 'partial', 'overdue'])->get()->sum->outstanding();

        return view('finance.reports', compact('from', 'to', 'byCategory', 'receivableOut', 'payableOut'));
    }
}
