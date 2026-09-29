@extends('layouts.app')
@section('title', 'Cash Flow Statement')
@section('content')
    <div class="view-head"><div><h2>Cash Flow Statement</h2><p class="sub">{{ $from }} → {{ $to }} · Net change: @money($net)</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('finance.cashflow-statement') }}" style="display:flex;gap:8px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form><button class="btn btn-ghost btn-sm" onclick="window.print()">Print</button></div>
    </div>
    <div class="stat-grid">
        <div class="stat-card"><div class="stat-value">@money($opening)</div><div class="stat-label">Opening cash</div></div>
        <div class="stat-card"><div class="stat-value">@money($receipts->sum())</div><div class="stat-label">Total receipts</div></div>
        <div class="stat-card"><div class="stat-value">@money($payments->sum())</div><div class="stat-label">Total payments</div></div>
        <div class="stat-card"><div class="stat-value">@money($closing)</div><div class="stat-label">Closing cash</div></div>
    </div>
    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Operating receipts</h3></div><div class="panel-body"><div class="legend">@forelse($receipts as $cat => $amt)<div class="legend-item"><span>{{ ucfirst($cat) }}</span><b>@money($amt)</b></div>@empty<div class="empty-state">None.</div>@endforelse</div></div></div>
        <div class="panel"><div class="panel-head"><h3>Operating payments</h3></div><div class="panel-body"><div class="legend">@forelse($payments as $cat => $amt)<div class="legend-item"><span>{{ ucfirst($cat) }}</span><b>@money($amt)</b></div>@empty<div class="empty-state">None.</div>@endforelse</div></div></div>
    </div>
@endsection
