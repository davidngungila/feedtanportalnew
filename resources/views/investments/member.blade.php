@extends('layouts.app')
@section('title', $member->name . ' · Investments')
@section('content')
    <div class="view-head">
        <div><h2>{{ $member->name }}</h2><p class="sub">{{ $member->member_no }} · {{ $member->phone }} · Full investment details for this member, one page at a time.</p></div>
        <div class="view-actions"><a href="{{ route('investments.matured') }}" class="btn btn-ghost">Back to matured</a><a href="{{ route('investments.create') }}" class="btn btn-primary">+ New investment</a></div>
    </div>

    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">Active invested</div><div class="bb-amount">@money($activeTotal)</div></div>
        <div class="balance-box"><div class="bb-label">Matured amount</div><div class="bb-amount">@money($maturedTotal)</div></div>
        <div class="balance-box"><div class="bb-label">Expected returns</div><div class="bb-amount">@money($expectedTotal)</div></div>
        <div class="balance-box"><div class="bb-label">Returns paid</div><div class="bb-amount">@money($paidReturns)</div></div>
        <div class="balance-box"><div class="bb-label">Pending payouts</div><div class="bb-amount">@money($pendingPayouts)</div><div class="bb-sub"><a href="{{ route('investments.matured.import') }}">Import page →</a></div></div>
    </div>

    <div class="table-card">
        <div class="table-toolbar"><strong>All investments ({{ $member->investments->count() }})</strong></div>
        <div class="table-scroll"><table>
            <thead><tr><th>No</th><th>Product / Plan</th><th>Amount</th><th>Return</th><th>Period</th><th>Status</th><th style="text-align:right;">Open</th></tr></thead>
            <tbody>
                @forelse($member->investments as $i)
                <tr><td><div class="cell-title"><a href="{{ route('investments.show', $i) }}">{{ $i->investment_no }}</a></div></td>
                <td>{{ $i->product->name ?? $i->plan ?? '—' }}</td><td class="cell-title">@money($i->amount)</td>
                <td>@money($i->expected_return) <span class="cell-sub">({{ $i->expected_return_rate }}%)</span><div class="cell-sub">Paid: @money($i->returns->sum('amount'))</div></td>
                <td><div class="cell-sub">{{ $i->start_date?->format('d M Y') }} → {{ $i->maturity_date?->format('d M Y') ?? '—' }}</div></td>
                <td><span class="tag {{ status_badge($i->status) }}">{{ ucfirst($i->status) }}</span></td>
                <td><div class="row-actions"><a href="{{ route('investments.show', $i) }}"><button type="button">↗</button></a></div></td></tr>
                @empty<tr><td colspan="7" class="empty-state">No investments for this member.</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>

    <div class="table-card">
        <div class="table-toolbar"><strong>Payouts &amp; SMS codes ({{ $member->payouts->count() }})</strong></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Code</th><th>Amount</th><th>Deductions</th><th>Net cash</th><th>Status</th><th>Decision</th></tr></thead>
            <tbody>
                @forelse($member->payouts as $p)
                <tr><td><div class="cell-title">{{ $p->verify_code }}</div><div class="cell-sub"><a href="{{ $p->shortUrl() }}" target="_blank">Open link</a></div></td>
                <td>@money($p->amount)</td>
                <td><div class="cell-sub">Loan @money($p->loan_installment) · SWF @money($p->swf_deduction) · Fines @money($p->fines_deduction) · T-shirt @money($p->tshirt_deduction) · CMG @money($p->capital_cmg)</div></td>
                <td class="cell-title">@money($p->net_cash)</td>
                <td><span class="tag {{ status_badge($p->status) }}">{{ ucfirst($p->status) }}</span></td>
                <td>{{ $p->decisionLabel() }}</td></tr>
                @empty<tr><td colspan="6" class="empty-state">No payouts for this member.</td></tr>@endforelse
            </tbody>
        </table></div>
    </div>
@endsection
