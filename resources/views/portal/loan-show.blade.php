@extends('layouts.app')
@section('title', $loan->loan_no)
@section('content')
    <div class="view-head">
        <div><h2>{{ $loan->loan_no }}</h2><p class="sub">{{ $member->name }} · Disbursed {{ $loan->disbursed_at?->format('d M Y') }} · Due {{ $loan->due_date?->format('d M Y') ?? '—' }}</p></div>
        <div class="view-actions"><a href="{{ route('portal.loans') }}" class="btn btn-ghost">Back</a></div>
    </div>
    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">Principal</div><div class="bb-amount">@money($loan->principal)</div><div class="bb-sub">{{ $loan->product->name ?? 'Standard loan' }} · {{ $loan->interest_rate }}%</div></div>
        <div class="balance-box"><div class="bb-label">Total payable</div><div class="bb-amount">@money($loan->total_payable)</div><div class="bb-sub">Principal + interest</div></div>
        <div class="balance-box"><div class="bb-label">Repaid</div><div class="bb-amount">@money($loan->totalRepaid())</div><div class="bb-sub">{{ $loan->repayments->count() }} payments</div></div>
        <div class="balance-box"><div class="bb-label">Balance</div><div class="bb-amount">@money($loan->outstanding())</div><div class="bb-sub"><span class="tag {{ status_badge($loan->status) }}">{{ ucfirst($loan->status) }}</span></div></div>
    </div>
    <div class="form-layout">
        <div class="table-card">
            <div class="table-toolbar"><span class="chip active">Repayments ({{ $loan->repayments->count() }})</span></div>
            <div class="table-scroll"><table>
                <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Notes</th></tr></thead>
                <tbody>
                    @forelse($loan->repayments as $r)
                    <tr><td>{{ ($r->paid_at ?? $r->created_at)->format('d M Y') }}</td><td class="cell-title">@money($r->amount)</td><td>{{ ucfirst($r->method ?? '—') }}</td><td>{{ $r->notes ?? '—' }}</td></tr>
                    @empty<tr><td colspan="4" class="empty-state">No repayments recorded yet.</td></tr>@endforelse
                </tbody>
            </table></div>
        </div>
        <div class="settings-panel"><h3>Make a payment</h3>
            @if($loan->outstanding() > 0)
            <form method="POST" action="{{ route('portal.loans.repay', $loan) }}">@csrf
                <div class="field"><label>Amount (max @money($loan->outstanding())) *</label><input type="number" name="amount" min="100" max="{{ $loan->outstanding() }}" step="100" required></div>
                <div class="field"><label>Date *</label><input type="date" name="paid_at" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required></div>
                <div class="field"><label>Method *</label><select name="method" required><option value="cash">Cash</option><option value="mobile">Mobile money</option><option value="bank">Bank</option></select></div>
                <button class="btn btn-primary" type="submit">Pay now</button>
            </form>
            @else
            <div class="empty-state">Fully repaid.</div>
            @endif
        </div>
    </div>
@endsection
