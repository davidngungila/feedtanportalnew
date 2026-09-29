@extends('layouts.app')
@section('title', $investment->investment_no)
@section('content')
    @php
        $paid = $investment->returns->sum('amount');
        $outstanding = max(0, (float) $investment->expected_return - $paid);
        $duration = ($investment->start_date && $investment->maturity_date) ? $investment->start_date->diffInDays($investment->maturity_date).' days' : '—';
    @endphp
    <div class="view-head"><div><h2>{{ $investment->investment_no }}</h2><p class="sub">{{ $investment->member->name ?? '—' }} · {{ $investment->product->name ?? $investment->plan ?? 'Plan' }} · Created {{ $investment->created_at->format('d M Y') }}</p></div>
    <div class="view-actions"><a href="{{ route('investments.index') }}" class="btn btn-ghost">Back</a><a href="{{ route('investments.edit', $investment) }}" class="btn btn-ghost">Edit</a><a href="{{ route('investment-returns.create', ['investment_id' => eid($investment->id)]) }}" class="btn btn-primary">+ Pay return</a></div></div>
    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">Invested amount</div><div class="bb-amount">@money($investment->amount)</div></div>
        <div class="balance-box"><div class="bb-label">Expected return ({{ $investment->expected_return_rate }}%)</div><div class="bb-amount">@money($investment->expected_return)</div></div>
        <div class="balance-box"><div class="bb-label">Returns paid</div><div class="bb-amount">@money($paid)</div><div class="bb-sub">{{ $investment->returns->count() }} payment(s)</div></div>
        <div class="balance-box"><div class="bb-label">Return outstanding</div><div class="bb-amount">@money($outstanding)</div></div>
        <div class="balance-box"><div class="bb-label">Status</div><div class="bb-amount"><span class="tag {{ status_badge($investment->status) }}">{{ ucfirst($investment->status) }}</span></div><div class="bb-sub">{{ $investment->start_date?->format('d M Y') }} → {{ $investment->maturity_date?->format('d M Y') ?? '—' }}</div></div>
    </div>

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head"><h3>Member</h3>@if($investment->member)<a class="link" href="{{ route('members.show', $investment->member) }}">Profile</a>@endif</div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Name</div><div class="dv">{{ $investment->member->name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Member no</div><div class="dv">{{ $investment->member->member_no ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $investment->member->phone ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Type</div><div class="dv">{{ $investment->member->memberType->name ?? '—' }}</div></div>
            </div>
            @if($investment->member)<div class="view-actions" style="margin-top:14px;"><a href="{{ route('investments.member', $investment->member) }}" class="btn btn-ghost btn-sm">Full member investments →</a></div>@endif</div>
        </div>
        <div class="panel">
            <div class="panel-head"><h3>Investment record (full)</h3><span class="link">{{ $investment->investment_no }}</span></div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Product</div><div class="dv">{{ $investment->product->name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Plan</div><div class="dv">{{ $investment->plan ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Start date</div><div class="dv">{{ $investment->start_date?->format('d M Y') ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Maturity date</div><div class="dv">{{ $investment->maturity_date?->format('d M Y') ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Duration</div><div class="dv">{{ $duration }}</div></div>
                <div class="detail-item"><div class="dk">Return rate</div><div class="dv">{{ $investment->expected_return_rate }}%</div></div>
                <div class="detail-item"><div class="dk">Total value</div><div class="dv">@money($investment->amount + $investment->expected_return)</div></div>
                <div class="detail-item"><div class="dk">Recorded by</div><div class="dv">{{ $investment->created_by ? (\App\Models\User::find($investment->created_by)->name ?? '#'.$investment->created_by) : '—' }}</div></div>
                <div class="detail-item"><div class="dk">Notes</div><div class="dv">{{ $investment->notes ?? '—' }}</div></div>
            </div></div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar"><strong>Payouts on this investment ({{ $investment->payouts->count() }})</strong><span class="cell-sub">SMS codes + member decisions</span></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Code</th><th>Amount</th><th>Net cash</th><th>Status</th><th>Decision</th><th>SMS</th></tr></thead>
            <tbody>
                @forelse($investment->payouts as $p)
                <tr><td><div class="cell-title"><a href="{{ route('payouts.show', $p) }}">{{ $p->verify_code }}</a></div></td>
                <td>@money($p->amount)</td><td class="cell-title">@money($p->net_cash)</td>
                <td><span class="tag {{ status_badge($p->status) }}">{{ ucfirst($p->status) }}</span></td>
                <td><div class="cell-sub">{{ $p->decisionLabel() }}</div></td>
                <td><div class="cell-sub">{{ $p->sms_sent_at?->format('d M Y H:i') ?? 'not sent' }}</div></td></tr>
                @empty<tr><td colspan="6" class="empty-state">No payouts linked to this investment.</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>

    <div class="table-card"><div class="table-toolbar"><strong>Return payments ({{ $investment->returns->count() }})</strong><a class="btn btn-ghost btn-sm" href="{{ route('investment-returns.create', ['investment_id' => eid($investment->id)]) }}">+ Pay return</a></div>
        <div class="table-scroll"><table><thead><tr><th>Amount</th><th>Date</th><th>Notes</th><th style="text-align:right;">Remove</th></tr></thead>
        <tbody>@forelse($investment->returns as $r)<tr><td class="cell-title">@money($r->amount)</td><td>{{ $r->paid_at?->format('d M Y') }}</td><td>{{ $r->notes ?? '—' }}</td>
        <td><form method="POST" action="{{ route('investment-returns.destroy', $r) }}" onsubmit="return confirm('Remove return?')">@csrf @method('DELETE')<div class="row-actions"><button type="submit" class="danger">✕</button></div></form></td></tr>
        @empty<tr><td colspan="4" class="empty-state">No returns paid yet.</td></tr>@endforelse</tbody></table></div>
    </div>
@endsection
