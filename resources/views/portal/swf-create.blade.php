@extends('layouts.app')
@section('title', 'New SWF Entry')
@section('content')
    <div class="view-head">
        <div><h2>New SWF entry</h2><p class="sub">{{ $member->name }} · Balance <b>@money($member->swfBalance())</b> · Recorded straight to your account.</p></div>
        <div class="view-actions"><a href="{{ route('portal.swf') }}" class="btn btn-ghost">Back</a></div>
    </div>
    <div class="form-layout">
        <div class="settings-panel"><h3>Entry details</h3>
            <form method="POST" action="{{ route('portal.swf.store') }}">@csrf
                <div class="form-row">
                    <div class="field"><label>Type *</label><select name="type" required><option value="contribution">Contribution (money in)</option><option value="deduction">Deduction</option><option value="payout">Payout</option><option value="claim">Claim</option></select></div>
                    <div class="field"><label>Method *</label><select name="method" required><option value="cash">Cash</option><option value="mobile">Mobile money</option><option value="bank">Bank</option></select></div>
                </div>
                <div class="form-row">
                    <div class="field"><label>Amount (TZS) *</label><input type="number" name="amount" min="100" step="100" required placeholder="e.g. 10000"></div>
                    <div class="field"><label>Date *</label><input type="date" name="transacted_at" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required></div>
                </div>
                <div class="field"><label>Reason</label><input name="reason" maxlength="255" placeholder="e.g. Monthly contribution"></div>
                <button class="btn btn-primary" type="submit">Save entry</button>
            </form>
        </div>
        <div class="settings-panel"><h3>Good to know</h3>
            <div class="toggle-row"><div class="toggle-text"><strong>Contributions</strong><span>Grow your welfare balance.</span></div></div>
            <div class="toggle-row"><div class="toggle-text"><strong>Payouts &amp; claims</strong><span>Cannot exceed your SWF balance.</span></div></div>
        </div>
    </div>
@endsection
