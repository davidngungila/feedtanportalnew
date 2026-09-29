@extends('layouts.app')
@section('title', 'Request a Loan')
@section('content')
    <div class="view-head">
        <div><h2>Request a loan</h2><p class="sub">{{ $member->name }} · Requests go to the loans office for review.</p></div>
        <div class="view-actions"><a href="{{ route('portal.loan-applications') }}" class="btn btn-ghost">Back</a></div>
    </div>
    <div class="form-layout">
        <div class="settings-panel"><h3>Loan request</h3>
            <form method="POST" action="{{ route('portal.loan-applications.store') }}">@csrf
                <div class="field"><label>Loan product</label>
                    <select name="loan_product_id"><option value="">— Standard —</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} · {{ $p->interest_rate }}% · up to @money($p->max_amount) </option>@endforeach</select>
                </div>
                <div class="field"><label>Amount (TZS) *</label><input type="number" name="amount" min="1000" step="100" required placeholder="e.g. 500000"></div>
                <div class="field"><label>Purpose</label><input name="purpose" maxlength="255" placeholder="e.g. Business stock"></div>
                <div class="field"><label>Notes</label><textarea name="notes" rows="3" placeholder="Anything the loans office should know"></textarea></div>
                <button class="btn btn-primary" type="submit">Send request</button>
            </form>
        </div>
        <div class="settings-panel"><h3>How it works</h3>
            <div class="toggle-row"><div class="toggle-text"><strong>1 · Send request</strong><span>Your request appears as pending instantly.</span></div></div>
            <div class="toggle-row"><div class="toggle-text"><strong>2 · Office review</strong><span>Loan officer approves or rejects with notes.</span></div></div>
            <div class="toggle-row"><div class="toggle-text"><strong>3 · Disbursement</strong><span>Approved loans appear under My loans with a schedule.</span></div></div>
        </div>
    </div>
@endsection
