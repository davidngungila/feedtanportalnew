@extends('layouts.app')
@section('title', 'New Deposit')
@section('content')
    <div class="view-head">
        <div><h2>New savings entry</h2><p class="sub">{{ $member->name }} · Balance <b>@money($member->savingsBalance())</b> · Recorded straight to your account.</p></div>
        <div class="view-actions"><a href="{{ route('portal.deposits') }}" class="btn btn-ghost">Back</a></div>
    </div>
    <div class="form-layout">
        <div class="settings-panel"><h3>Entry details</h3>
            <form method="POST" action="{{ route('portal.deposits.store') }}">@csrf
                <div class="form-row">
                    <div class="field"><label>Type *</label><select name="type" required><option value="deposit">Deposit (money in)</option><option value="withdrawal">Withdrawal (money out)</option></select></div>
                    <div class="field"><label>Product</label><select name="deposit_product_id"><option value="">— Standard —</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
                </div>
                <div class="form-row">
                    <div class="field"><label>Amount (TZS) *</label><input type="number" name="amount" min="100" step="100" required placeholder="e.g. 50000"></div>
                    <div class="field"><label>Method *</label><select name="method" required><option value="cash">Cash</option><option value="mobile">Mobile money</option><option value="bank">Bank</option></select></div>
                </div>
                <div class="field"><label>Date *</label><input type="date" name="transacted_at" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required></div>
                <div class="field"><label>Notes</label><textarea name="notes" rows="2" placeholder="Optional note"></textarea></div>
                <button class="btn btn-primary" type="submit">Save entry</button>
            </form>
        </div>
        <div class="settings-panel"><h3>Good to know</h3>
            <div class="toggle-row"><div class="toggle-text"><strong>Deposits</strong><span>Add to your savings instantly.</span></div></div>
            <div class="toggle-row"><div class="toggle-text"><strong>Withdrawals</strong><span>Cannot exceed your savings balance.</span></div></div>
            <div class="toggle-row"><div class="toggle-text"><strong>Receipts</strong><span>Every entry gets a receipt number and hits the books.</span></div></div>
        </div>
    </div>
@endsection
