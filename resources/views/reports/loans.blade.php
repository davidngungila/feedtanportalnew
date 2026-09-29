@extends('layouts.app')
@section('title', 'Loan Reports')
@section('content')
    <div class="view-head"><div><h2>Loan Reports</h2><p class="sub">Disbursed @money($disbursed) · Repaid @money($repaid) · Outstanding @money($outstanding)</p></div>
        <div class="view-actions"><form method="GET" action="{{ route('reports.loans') }}" style="display:flex;gap:8px;"><input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"><input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-weight:600;"></form><button class="btn btn-ghost btn-sm" onclick="window.print()">Print</button></div>
    </div>
    <div class="stat-grid">
        <div class="stat-card"><div class="stat-value">@money($disbursed)</div><div class="stat-label">Disbursed in period</div></div>
        <div class="stat-card"><div class="stat-value">@money($repaid)</div><div class="stat-label">Repaid in period</div></div>
        <div class="stat-card"><div class="stat-value">@money($outstanding)</div><div class="stat-label">Total outstanding</div></div>
    </div>
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Loan</th><th>Member</th><th>Product</th><th>Principal</th><th>Payable</th><th>Status</th></tr></thead>
        <tbody>@forelse($loans as $l)<tr><td class="cell-title"><a href="{{ route('loans.show', $l) }}">{{ $l->loan_no }}</a></td><td>{{ $l->member->name ?? '—' }}</td><td>{{ $l->product->name ?? '—' }}</td><td>@money($l->principal)</td><td>@money($l->total_payable)</td><td><span class="tag {{ status_badge($l->status) }}">{{ ucfirst($l->status) }}</span></td></tr>@empty<tr><td colspan="6" class="empty-state">No loans in period.</td></tr>@endforelse</tbody>
    </table></div><div class="table-pager"><span class="pager-info">{{ $loans->total() }} loans</span><div class="pager-pages">{{ $loans->links('pagination.pager') }}</div></div></div>
@endsection
