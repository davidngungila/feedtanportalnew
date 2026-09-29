@extends('layouts.app')
@section('title', 'New Investment')
@section('content')
    <div class="view-head">
        <div><h2>Place an investment</h2><p class="sub">{{ $member->name }} · Pick a product — return and maturity are set automatically.</p></div>
        <div class="view-actions"><a href="{{ route('portal.investments') }}" class="btn btn-ghost">Back</a></div>
    </div>
    <div class="form-layout">
        <div class="settings-panel"><h3>Investment details</h3>
            <form method="POST" action="{{ route('portal.investments.store') }}">@csrf
                <div class="field"><label>Product *</label>
                    <select name="investment_product_id" required><option value="">Select…</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} · {{ $p->return_rate }}%@if($p->min_amount) · min @money($p->min_amount) @endif @if($p->duration_months) · {{ $p->duration_months }} months @endif</option>@endforeach</select>
                </div>
                <div class="form-row">
                    <div class="field"><label>Amount (TZS) *</label><input type="number" name="amount" min="1000" step="100" required placeholder="e.g. 200000"></div>
                    <div class="field"><label>Start date *</label><input type="date" name="start_date" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required></div>
                </div>
                <div class="field"><label>Notes</label><textarea name="notes" rows="2" placeholder="Optional note"></textarea></div>
                <button class="btn btn-primary" type="submit">Place investment</button>
            </form>
        </div>
        <div class="settings-panel"><h3>Good to know</h3>
            <div class="toggle-row"><div class="toggle-text"><strong>Active immediately</strong><span>Your placement starts earning from the start date.</span></div></div>
            <div class="toggle-row"><div class="toggle-text"><strong>Track maturity</strong><span>Follow progress under My Investments.</span></div></div>
        </div>
    </div>
@endsection
