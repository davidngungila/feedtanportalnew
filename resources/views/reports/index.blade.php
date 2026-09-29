@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="view-head">
        <div><h2>Reports</h2><p class="sub">Member balances and period activity across all four funds.</p></div>
        <div class="view-actions">
            <form method="GET" action="{{ route('reports.index') }}" style="display:flex;gap:8px;align-items:center;">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:var(--white);font-weight:600;">
                <input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()" style="padding:9px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:var(--white);font-weight:600;">
                <button class="btn btn-ghost btn-sm" type="submit">Apply</button>
            </form>
        </div>
    </div>

    <div class="tabs">
        @foreach(['summary'=>'Member balances','loans'=>'Loans','deposits'=>'Deposits','investments'=>'Investments','swf'=>'SWF'] as $k=>$v)
        <a href="{{ route('reports.index', ['tab'=>$k,'from'=>$from,'to'=>$to]) }}" class="tab-btn {{ $tab===$k?'active':'' }}">{{ $v }}</a>
        @endforeach
    </div>

    @if($tab==='summary')
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Member</th><th>Savings</th><th>Loans out</th><th>Invested</th><th>SWF</th></tr></thead>
        <tbody>@forelse($members as $r)<tr><td><div class="cell-title">{{ $r['member']->name }}</div><div class="cell-sub">{{ $r['member']->member_no }} · {{ $r['member']->phone }}</div></td><td>@money($r['savings'])</td><td>@money($r['loan_outstanding'])</td><td>@money($r['invested'])</td><td>@money($r['swf'])</td></tr>@empty<tr><td colspan="5" class="empty-state">No members.</td></tr>@endforelse</tbody>
    </table></div></div>
    @elseif($tab==='loans')
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Loan</th><th>Member</th><th>Principal</th><th>Payable</th><th>Status</th></tr></thead>
        <tbody>@forelse($loans as $l)<tr><td class="cell-title">{{ $l->loan_no }}</td><td>{{ $l->member->name ?? '—' }}</td><td>@money($l->principal)</td><td>@money($l->total_payable)</td><td><span class="tag {{ status_badge($l->status) }}">{{ ucfirst($l->status) }}</span></td></tr>@empty<tr><td colspan="5" class="empty-state">No loans in period.</td></tr>@endforelse</tbody>
    </table></div></div>
    @elseif($tab==='deposits')
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Receipt</th><th>Member</th><th>Type</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>@forelse($deposits as $d)<tr><td class="cell-title">{{ $d->receipt_no }}</td><td>{{ $d->member->name ?? '—' }}</td><td>{{ ucfirst($d->type) }}</td><td>@money($d->amount)</td><td>{{ $d->transacted_at?->format('d M Y') }}</td></tr>@empty<tr><td colspan="5" class="empty-state">No deposits in period.</td></tr>@endforelse</tbody>
    </table></div></div>
    @elseif($tab==='investments')
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>No</th><th>Member</th><th>Amount</th><th>Return</th><th>Status</th></tr></thead>
        <tbody>@forelse($investments as $i)<tr><td class="cell-title">{{ $i->investment_no }}</td><td>{{ $i->member->name ?? '—' }}</td><td>@money($i->amount)</td><td>@money($i->expected_return)</td><td>{{ ucfirst($i->status) }}</td></tr>@empty<tr><td colspan="5" class="empty-state">No investments in period.</td></tr>@endforelse</tbody>
    </table></div></div>
    @else
    <div class="table-card"><div class="table-scroll"><table>
        <thead><tr><th>Receipt</th><th>Member</th><th>Type</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>@forelse($swf as $s)<tr><td class="cell-title">{{ $s->receipt_no }}</td><td>{{ $s->member->name ?? '—' }}</td><td>{{ ucfirst($s->type) }}</td><td>@money($s->amount)</td><td>{{ $s->transacted_at?->format('d M Y') }}</td></tr>@empty<tr><td colspan="5" class="empty-state">No SWF in period.</td></tr>@endforelse</tbody>
    </table></div></div>
    @endif
@endsection
