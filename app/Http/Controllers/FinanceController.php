<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\JournalLine;
use App\Models\Receivable;

class FinanceController extends Controller
{
    public function overview()
    {
        $income = (float) FinanceTransaction::where('type', 'income')->sum('amount');
        $expenses = (float) FinanceTransaction::where('type', 'expense')->sum('amount');

        $cashIds = \App\Services\FinancePosting::cashAccountIds();
        $cashBalance = FinanceAccount::whereIn('id', $cashIds)->get()->sum(fn ($a) => $a->balance());

        $receivableOut = (float) Receivable::where('kind', 'receivable')->whereIn('status', ['open', 'partial', 'overdue'])->get()->sum->outstanding();
        $payableOut = (float) Receivable::where('kind', 'payable')->whereIn('status', ['open', 'partial', 'overdue'])->get()->sum->outstanding();

        $byType = FinanceAccount::whereIn('type', ['asset', 'liability', 'equity', 'income', 'expense'])
            ->get()->groupBy('type')->map(fn ($g) => $g->sum(fn ($a) => $a->balance()));

        $recent = FinanceTransaction::with(['account', 'member'])->latest()->limit(8)->get();
        $recentJournals = \App\Models\JournalEntry::withCount('lines')->latest()->limit(5)->get();

        return view('finance.overview', compact('income', 'expenses', 'cashBalance', 'receivableOut', 'payableOut', 'byType', 'recent', 'recentJournals'));
    }

    public function cashflow()
    {
        $from = request('from', now()->startOfMonth()->toDateString());
        $to = request('to', now()->toDateString());

        $txns = FinanceTransaction::with('account')->whereBetween('transacted_at', [$from, $to])->get();
        $in = $txns->where('type', 'income')->groupBy('category')->map->sum('amount');
        $out = $txns->where('type', 'expense')->groupBy('category')->map->sum('amount');

        $cashIds = \App\Services\FinancePosting::cashAccountIds();
        $closing = FinanceAccount::whereIn('id', $cashIds)->get()->sum(fn ($a) => $a->balance(null, $to));
        $net = $in->sum() - $out->sum();
        $opening = $closing - $net;

        return view('finance.cashflow', compact('from', 'to', 'in', 'out', 'opening', 'closing', 'net'));
    }
}
