@extends('layouts.app')
@section('title', 'Financial Reports')
@section('content')
    <div class="view-head"><div><h2>Financial Reports</h2><p class="sub">{{ $from }} → {{ $to }}</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('finance.reports') }}" style="display:flex;gap:8px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form></div>
    </div>
    <div class="stat-grid">
        <div class="stat-card"><div class="stat-value">@money($receivableOut)</div><div class="stat-label">Receivables outstanding</div></div>
        <div class="stat-card"><div class="stat-value">@money($payableOut)</div><div class="stat-label">Payables outstanding</div></div>
    </div>
    <div class="table-card"><div class="table-toolbar"><strong>By category</strong></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Type</th><th>Category</th><th>Count</th><th>Total</th></tr></thead>
            <tbody>@forelse($byCategory as $r)<tr><td><span class="tag {{ $r['type'] === 'income' ? 'tag-green' : 'tag-red' }}">{{ ucfirst($r['type']) }}</span></td><td>{{ $r['category'] }}</td><td>{{ $r['count'] }}</td><td class="cell-title">@money($r['total'])</td></tr>@empty<tr><td colspan="4" class="empty-state">No activity in period.</td></tr>@endforelse</tbody>
        </table></div>
    </div>
    <div class="card-grid">
        <div class="mini-card"><div class="mc-top"><span class="mc-name">Income Statement</span></div><div class="view-actions" style="margin-top:12px;"><a href="{{ route('finance.income-statement', ['from' => $from, 'to' => $to]) }}" class="btn btn-ghost btn-sm">Open</a></div></div>
        <div class="mini-card"><div class="mc-top"><span class="mc-name">Balance Sheet</span></div><div class="view-actions" style="margin-top:12px;"><a href="{{ route('finance.balance-sheet', ['as_of' => $to]) }}" class="btn btn-ghost btn-sm">Open</a></div></div>
        <div class="mini-card"><div class="mc-top"><span class="mc-name">Cash Flow Statement</span></div><div class="view-actions" style="margin-top:12px;"><a href="{{ route('finance.cashflow-statement', ['from' => $from, 'to' => $to]) }}" class="btn btn-ghost btn-sm">Open</a></div></div>
    </div>
@endsection
