@extends('layouts.app')
@section('title', $payout->verify_code)
@section('content')
    <div class="view-head">
        <div><h2>Payout {{ $payout->verify_code }}</h2><p class="sub">{{ $payout->member->name ?? '—' }} · {{ $payout->phone }} · Imported {{ $payout->created_at->format('d M Y') }}</p></div>
        <div class="view-actions"><a href="{{ route('investments.matured.import') }}" class="btn btn-ghost">Back to import</a>
            @if($payout->status === 'verified')
            <form method="POST" action="{{ route('payouts.pay', $payout) }}" onsubmit="return confirm('Pay @money($payout->net_cash) and apply deductions?')" style="display:inline;">@csrf<button class="btn btn-primary" type="submit">Pay now</button></form>
            @endif
            @if($payout->status === 'pending')
            <form method="POST" action="{{ route('payouts.sms.single', $payout) }}" style="display:inline;">@csrf<button class="btn btn-primary" type="submit">Send SMS</button></form>
            @endif
        </div>
    </div>

    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">Matured amount</div><div class="bb-amount">@money($payout->amount)</div></div>
        <div class="balance-box"><div class="bb-label">Total deductions</div><div class="bb-amount">@money($payout->loan_installment + $payout->swf_deduction + $payout->fines_deduction + $payout->tshirt_deduction + $payout->capital_cmg)</div></div>
        <div class="balance-box"><div class="bb-label">Net cash</div><div class="bb-amount">@money($payout->net_cash)</div></div>
        <div class="balance-box"><div class="bb-label">Status</div><div class="bb-amount"><span class="tag {{ status_badge($payout->status) }}">{{ ucfirst($payout->status) }}</span></div><div class="bb-sub">SMS: {{ $payout->sms_sent_at?->format('d M Y H:i') ?? 'not sent' }}</div></div>
    </div>

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head"><h3>Sheet details (all columns)</h3><span class="link">{{ $payout->verify_code }}</span></div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Member</div><div class="dv"><a href="{{ route('members.show', $payout->member) }}">{{ $payout->member->name ?? '—' }}</a></div></div>
                <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $payout->phone }} <span class="cell-sub">({{ $payout->intlPhone() }})</span></div></div>
                <div class="detail-item"><div class="dk">Investment</div><div class="dv">@if($payout->investment)<a href="{{ route('investments.show', $payout->investment) }}">{{ $payout->investment->investment_no }}</a>@else — @endif</div></div>
                <div class="detail-item"><div class="dk">SMS link</div><div class="dv"><a href="{{ $payout->shortUrl() }}" target="_blank">{{ $payout->shortUrl() }}</a></div></div>
                <div class="detail-item"><div class="dk">Amount Earned</div><div class="dv">@money($payout->amount)</div></div>
                <div class="detail-item"><div class="dk">Loan installment</div><div class="dv">@money($payout->loan_installment)</div></div>
                <div class="detail-item"><div class="dk">SWF deduction</div><div class="dv">@money($payout->swf_deduction)</div></div>
                <div class="detail-item"><div class="dk">Fines deduction</div><div class="dv">@money($payout->fines_deduction)</div></div>
                <div class="detail-item"><div class="dk">T-shirt deduction</div><div class="dv">@money($payout->tshirt_deduction)</div></div>
                <div class="detail-item"><div class="dk">Capital FeedTan CMG</div><div class="dv">@money($payout->capital_cmg)</div></div>
                <div class="detail-item"><div class="dk">Net cash</div><div class="dv">@money($payout->net_cash)</div></div>
                @if($payout->notes)<div class="detail-item"><div class="dk">Import note</div><div class="dv">{{ $payout->notes }}</div></div>@endif
            </div></div>
        </div>
        <div class="panel">
            <div class="panel-head"><h3>Member decision</h3><span class="link">{{ $payout->verified_at?->format('d M Y') ?? 'awaiting' }}</span></div>
            <div class="panel-body">
                @if($payout->allocation)
                <div class="receipt">
                    @foreach(['cash' => 'Taslimu', 'swf' => 'SWF', 'loan' => 'Mkopo', 'shares' => 'Hisa za duka', 'reinvest' => 'Wekeza tena', 'savings' => 'Akiba'] as $k => $label)
                        @if(($payout->allocation[$k] ?? 0) > 0)
                        <div class="receipt-row"><span>{{ $label }}@if($k === 'reinvest') (miaka {{ $payout->allocation['reinvest_term'] ?? '?' }})@endif @if($k === 'savings') ({{ strtoupper($payout->allocation['savings_type'] ?? '') }})@endif</span><b>@money($payout->allocation[$k])</b></div>
                        @endif
                    @endforeach
                </div>
                @else
                <div class="empty-state">{{ $payout->decisionLabel() }}</div>
                @endif
                @if($payout->decision_notes)<div class="receipt" style="margin-top:10px;"><div class="receipt-row"><span>Notes</span><b>{{ $payout->decision_notes }}</b></div></div>@endif
                @if($payout->paid_at)<div class="detail-item" style="margin-top:10px;"><div class="dk">Paid at</div><div class="dv">{{ $payout->paid_at->format('d M Y H:i') }}</div></div>@endif
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar"><strong>Applied postings (matched by code)</strong></div>
        <div class="table-scroll"><table>
            <thead><tr><th>Kind</th><th>Reference</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($postings['returns'] as $r)<tr><td>Return paid</td><td class="cell-title">{{ $r->investment->investment_no ?? '#' }}</td><td>@money($r->amount)</td><td>{{ $r->paid_at?->format('d M Y') }}</td></tr>@empty @endforelse
                @forelse($postings['repayments'] as $r)<tr><td>Loan repayment</td><td class="cell-title">{{ $r->receipt_no }}</td><td>@money($r->amount)</td><td>{{ $r->paid_at?->format('d M Y') }}</td></tr>@empty @endforelse
                @forelse($postings['swf'] as $r)<tr><td>SWF contribution</td><td class="cell-title">{{ $r->receipt_no }}</td><td>@money($r->amount)</td><td>{{ $r->transacted_at?->format('d M Y') }}</td></tr>@empty @endforelse
                @forelse($postings['finance'] as $r)<tr><td>Book income</td><td class="cell-title">{{ $r->reference }}</td><td>@money($r->amount)</td><td>{{ $r->transacted_at?->format('d M Y') }}</td></tr>@empty @endforelse
                @if($postings['returns']->isEmpty() && $postings['repayments']->isEmpty() && $postings['swf']->isEmpty() && $postings['finance']->isEmpty())
                <tr><td colspan="4" class="empty-state">Nothing posted yet — pay after verification.</td></tr>
                @endif
            </tbody>
        </table></div>
    </div>

    <div class="table-card">
        <div class="table-toolbar"><strong>SMS history ({{ $postings['sms']->count() }})</strong></div>
        <div class="table-scroll"><table>
            <thead><tr><th>When</th><th>Phone</th><th>Status</th><th>Response</th></tr></thead>
            <tbody>@forelse($postings['sms'] as $s)<tr><td class="cell-sub">{{ $s->created_at->format('d M Y H:i') }}</td><td>{{ $s->phone }}</td><td><span class="tag {{ $s->status === 'sent' ? 'tag-green' : 'tag-red' }}">{{ ucfirst($s->status) }}</span></td><td class="cell-sub">{{ \Illuminate\Support\Str::limit($s->provider_response ?? '', 120) }}</td></tr>
            @empty<tr><td colspan="4" class="empty-state">No SMS sent yet.</td></tr>@endforelse</tbody>
        </table></div>
    </div>
@endsection
