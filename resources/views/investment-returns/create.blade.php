@extends('layouts.app')
@section('title', 'Pay Investment Return')
@section('content')
    <div class="view-head"><div><h2>Pay Investment Return</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('investment-returns.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Payment details</h3>
        <form method="POST" action="{{ route('investment-returns.store') }}">@csrf
            <div class="field"><label>Investment *</label><select name="investment_id" required><option value="">Select…</option>@foreach($investments as $i)<option value="{{ $i->id }}" {{ (string)$selected === (string)$i->id ? 'selected' : '' }}>{{ $i->investment_no }} · {{ $i->member->name ?? '' }} · @money($i->amount)</option>@endforeach</select></div>
            <div class="form-row"><div class="field"><label>Amount *</label><input type="number" name="amount" min="100" step="100" required></div><div class="field"><label>Paid date *</label><input type="date" name="paid_at" value="{{ now()->toDateString() }}" required></div></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save payment</button>
        </form>
    </div>
@endsection
