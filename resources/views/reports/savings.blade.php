@extends('layouts.app')
@section('title', 'Savings & Investment Reports')
@section('content')
    <div class="view-head"><div><h2>Savings &amp; Investment Reports</h2><p class="sub">Deposits @money($depIn) · Withdrawals @money($depOut) · New investments @money($invTotal)</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('reports.savings') }}" style="display:flex;gap:8px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form><button class="btn btn-ghost btn-sm" onclick="window.print()">Print</button></div>
    </div>
    <div class="stat-grid">
        <div class="stat-card"><div class="stat-value">@money($depIn)</div><div class="stat-label">Deposits in</div></div>
        <div class="stat-card"><div class="stat-value">@money($depOut)</div><div class="stat-label">Withdrawals out</div></div>
        <div class="stat-card"><div class="stat-value">@money($depIn - $depOut)</div><div class="stat-label">Net savings</div></div>
        <div class="stat-card"><div class="stat-value">@money($invTotal)</div><div class="stat-label">New investments</div></div>
    </div>
    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Deposits</h3></div>
            <div class="table-scroll"><table style="min-width:420px;"><thead><tr><th>Receipt</th><th>Member</th><th>Amount</th></tr></thead>
            <tbody>@forelse($deposits as $d)<tr><td class="cell-title">{{ $d->receipt_no }}<div class="cell-sub">{{ ucfirst($d->type) }}</div></td><td>{{ $d->member->name ?? '—' }}</td><td>@money($d->amount)</td></tr>@empty<tr><td colspan="3" class="empty-state">None.</td></tr>@endforelse</tbody></table></div>
            <div class="table-pager"><span class="pager-info">{{ $deposits->total() }}</span><div class="pager-pages">{{ $deposits->links('pagination.pager') }}</div></div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Investments</h3></div>
            <div class="table-scroll"><table style="min-width:420px;"><thead><tr><th>No</th><th>Member</th><th>Amount</th></tr></thead>
            <tbody>@forelse($investments as $i)<tr><td class="cell-title">{{ $i->investment_no }}</td><td>{{ $i->member->name ?? '—' }}</td><td>@money($i->amount)</td></tr>@empty<tr><td colspan="3" class="empty-state">None.</td></tr>@endforelse</tbody></table></div>
            <div class="table-pager"><span class="pager-info">{{ $investments->total() }}</span><div class="pager-pages">{{ $investments->links('pagination.pager') }}</div></div>
        </div>
    </div>
@endsection
