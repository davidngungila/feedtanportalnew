@extends('layouts.app')
@section('title', 'Cash Flow')
@section('content')
    <div class="view-head"><div><h2>Cash Flow</h2><p class="sub">Receipts vs payments in the period. Net: @money($net)</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('finance.cashflow') }}" style="display:flex;gap:8px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form></div>
    </div>
    <div class="stat-grid">
        <div class="stat-card" style="--stat-tint:var(--sand-200);--stat-fg:var(--coffee-700);"><div class="stat-value">@money($opening)</div><div class="stat-label">Opening cash</div></div>
        <div class="stat-card" style="--stat-tint:var(--acacia-100);--stat-fg:var(--acacia-600);"><div class="stat-value">@money($in->sum())</div><div class="stat-label">Receipts</div></div>
        <div class="stat-card" style="--stat-tint:var(--danger-100);--stat-fg:var(--danger);"><div class="stat-value">@money($out->sum())</div><div class="stat-label">Payments</div></div>
        <div class="stat-card" style="--stat-tint:var(--gold-100);--stat-fg:#8a6418;"><div class="stat-value">@money($closing)</div><div class="stat-label">Closing cash</div></div>
    </div>
    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Receipts by category</h3></div><div class="panel-body"><div class="legend">@forelse($in as $cat => $amt)<div class="legend-item"><span>{{ ucfirst($cat) }}</span><b>@money($amt)</b></div>@empty<div class="empty-state">No receipts.</div>@endforelse</div></div></div>
        <div class="panel"><div class="panel-head"><h3>Payments by category</h3></div><div class="panel-body"><div class="legend">@forelse($out as $cat => $amt)<div class="legend-item"><span>{{ ucfirst($cat) }}</span><b>@money($amt)</b></div>@empty<div class="empty-state">No payments.</div>@endforelse</div></div></div>
    </div>
@endsection
