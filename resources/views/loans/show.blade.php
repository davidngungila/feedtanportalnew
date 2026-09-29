@extends('layouts.app')

@section('title', $loan->loan_no)

@section('content')
    <div class="view-head">
        <div><h2>{{ $loan->loan_no }}</h2><p class="sub">{{ $loan->member->name ?? '—' }} · {{ $loan->member->phone ?? '' }} · {{ $loan->purpose ?? 'Loan' }} · {{ $loan->product->name ?? 'No product' }}</p></div>
        <div class="view-actions"><a href="{{ route('loans.index') }}" class="btn btn-ghost">Back</a><a href="{{ route('loans.edit', $loan) }}" class="btn btn-ghost">Edit loan</a></div>
    </div>

    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">Principal</div><div class="bb-amount">@money($loan->principal)</div><div class="bb-sub">{{ $loan->interest_rate }}% interest = @money($loan->interest_amount)</div></div>
        <div class="balance-box"><div class="bb-label">Total payable</div><div class="bb-amount">@money($loan->total_payable)</div><div class="bb-sub">Due {{ $loan->due_date?->format('d M Y') ?? '—' }}</div></div>
        <div class="balance-box"><div class="bb-label">Repaid</div><div class="bb-amount">@money($loan->totalRepaid())</div><div class="bb-sub">{{ $loan->repayments->count() }} payments</div></div>
        <div class="balance-box"><div class="bb-label">Outstanding</div><div class="bb-amount">@money($loan->outstanding())</div><div class="bb-sub"><span class="tag {{ status_badge($loan->status) }}">{{ ucfirst($loan->status) }}</span></div></div>
    </div>

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head"><h3>Repayments</h3><span class="link">{{ $loan->repayments->count() }}</span></div>
            <div class="table-scroll"><table style="min-width:520px;">
                <thead><tr><th>Receipt</th><th>Amount</th><th>Date</th><th>Method</th><th></th></tr></thead>
                <tbody>
                    @forelse($loan->repayments as $r)
                    <tr><td class="cell-title">{{ $r->receipt_no }}</td><td>@money($r->amount)</td><td>{{ $r->paid_at?->format('d M Y') }}</td><td>{{ ucfirst($r->method) }}</td>
                    <td><form method="POST" action="{{ route('repayments.destroy',$r) }}" onsubmit="return confirm('Remove repayment?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm" type="submit">Remove</button></form></td></tr>
                    @empty<tr><td colspan="5" class="empty-state">No repayments yet. Use the form on this page.</td></tr>@endforelse
                </tbody>
            </table></div>
        </div>
        <div class="panel">
            <div class="panel-head"><h3>Record repayment</h3><span class="link">Page form</span></div>
            <div class="panel-body">
                @if($loan->outstanding() > 0)
                <form method="POST" action="{{ route('loans.repay',$loan) }}">
                    @csrf
                    <div class="field"><label>Amount (max @money($loan->outstanding()))</label><input type="number" name="amount" min="100" max="{{ $loan->outstanding() }}" step="100" required></div>
                    <div class="form-row"><div class="field"><label>Paid date</label><input type="date" name="paid_at" value="{{ now()->toDateString() }}" required></div><div class="field"><label>Method</label><select name="method"><option value="cash">Cash</option><option value="mobile">Mobile</option><option value="bank">Bank</option></select></div></div>
                    <div class="field"><label>Notes</label><textarea name="notes" rows="2"></textarea></div>
                    <button class="btn btn-primary" type="submit">Save repayment</button>
                </form>
                @else
                <div class="empty-state">Fully repaid.</div>
                @endif
                <div class="receipt">
                    <div class="receipt-row"><span>Member</span><b>{{ $loan->member->name ?? '—' }}</b></div>
                    <div class="receipt-row"><span>Disbursed</span><b>{{ $loan->disbursed_at?->format('d M Y') }}</b></div>
                    <div class="receipt-row"><span>Principal</span><b>@money($loan->principal)</b></div>
                    <div class="receipt-row"><span>Interest</span><b>@money($loan->interest_amount)</b></div>
                    <div class="receipt-row"><span>Payable</span><b>@money($loan->total_payable)</b></div>
                </div>
            </div>
        </div>
    </div>

    @include('finance.postings', ['journals' => $journals])
@endsection
