@extends('layouts.app')
@section('title', 'Financial Statements')
@section('content')
    <div class="view-head"><div><h2>Financial Statements</h2><p class="sub">Pick a statement — all derived from posted journals.</p></div></div>
    <div class="card-grid">
        <div class="mini-card"><div class="mc-top"><span class="mc-name">Income Statement</span></div><div class="mc-label">Revenue vs expenses for a period.</div><div class="view-actions" style="margin-top:12px;"><a href="{{ route('finance.income-statement') }}" class="btn btn-primary btn-sm">Open</a></div></div>
        <div class="mini-card"><div class="mc-top"><span class="mc-name">Balance Sheet</span></div><div class="mc-label">Assets = liabilities + equity at a date.</div><div class="view-actions" style="margin-top:12px;"><a href="{{ route('finance.balance-sheet') }}" class="btn btn-primary btn-sm">Open</a></div></div>
        <div class="mini-card"><div class="mc-top"><span class="mc-name">Cash Flow Statement</span></div><div class="mc-label">Receipts, payments and net change.</div><div class="view-actions" style="margin-top:12px;"><a href="{{ route('finance.cashflow-statement') }}" class="btn btn-primary btn-sm">Open</a></div></div>
        <div class="mini-card"><div class="mc-top"><span class="mc-name">Trial Balance</span></div><div class="mc-label">Debits = credits proof from the ledger.</div><div class="view-actions" style="margin-top:12px;"><a href="{{ route('finance.trial-balance') }}" class="btn btn-primary btn-sm">Open</a></div></div>
        <div class="mini-card"><div class="mc-top"><span class="mc-name">Financial Reports</span></div><div class="mc-label">Category breakdowns and outstanding.</div><div class="view-actions" style="margin-top:12px;"><a href="{{ route('finance.reports') }}" class="btn btn-primary btn-sm">Open</a></div></div>
    </div>
@endsection
