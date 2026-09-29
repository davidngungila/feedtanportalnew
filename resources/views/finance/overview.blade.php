@extends('layouts.app')
@section('title', 'Finance Overview')
@section('content')
    <div class="view-head">
        <div><h2>Finance Overview</h2><p class="sub">Institution books at a glance — every entry auto-posts to the ledger.</p></div>
        <div class="view-actions"><a href="{{ route('finance.transactions.create') }}" class="btn btn-primary">+ New entry</a></div>
    </div>

    <div class="stat-grid">
        <div class="stat-card" style="--stat-tint:var(--acacia-100);--stat-fg:var(--acacia-600);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg></div></div><div class="stat-value">@money($income)</div><div class="stat-label">Total income</div></div>
        <div class="stat-card" style="--stat-tint:var(--danger-100);--stat-fg:var(--danger);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg></div></div><div class="stat-value">@money($expenses)</div><div class="stat-label">Total expenses</div></div>
        <div class="stat-card" style="--stat-tint:var(--terracotta-100);--stat-fg:var(--terracotta-600);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l9-9 5 5 4-4"></path></svg></div></div><div class="stat-value">@money($income - $expenses)</div><div class="stat-label">Net surplus</div></div>
        <div class="stat-card" style="--stat-tint:var(--gold-100);--stat-fg:#8a6418;"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg></div></div><div class="stat-value">@money($cashBalance)</div><div class="stat-label">Cash on hand + bank</div></div>
        <div class="stat-card" style="--stat-tint:var(--sand-200);--stat-fg:var(--coffee-700);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div></div><div class="stat-value">@money($receivableOut)</div><div class="stat-label">Receivables outstanding</div></div>
        <div class="stat-card" style="--stat-tint:var(--sand-200);--stat-fg:var(--coffee-700);"><div class="stat-top"><div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div></div><div class="stat-value">@money($payableOut)</div><div class="stat-label">Payables outstanding</div></div>
    </div>

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head"><h3>Recent entries</h3><a href="{{ route('finance.transactions') }}" class="link">View all</a></div>
            <div class="table-scroll"><table style="min-width:520px;">
                <thead><tr><th>Reference</th><th>Account</th><th>Amount</th><th>Type</th></tr></thead>
                <tbody>@forelse($recent as $t)<tr><td><div class="cell-title">{{ $t->reference }}</div><div class="cell-sub">{{ $t->transacted_at?->format('d M Y') }}</div></td><td>{{ $t->account->name ?? '—' }}</td><td class="cell-title">@money($t->amount)</td><td><span class="tag {{ $t->type === 'income' ? 'tag-green' : 'tag-red' }}">{{ ucfirst($t->type) }}</span></td></tr>@empty<tr><td colspan="4" class="empty-state">No entries yet.</td></tr>@endforelse</tbody>
            </table></div>
        </div>
        <div class="panel">
            <div class="panel-head"><h3>Balance by class</h3><a href="{{ route('finance.chart') }}" class="link">Chart</a></div>
            <div class="panel-body"><div class="legend">
                @foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'income' => 'Income', 'expense' => 'Expenses'] as $k => $v)
                <div class="legend-item"><span>{{ $v }}</span><b>@money($byType[$k] ?? 0)</b></div>
                @endforeach
            </div></div>
        </div>
    </div>
@endsection
