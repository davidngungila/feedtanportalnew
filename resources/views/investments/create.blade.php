@extends('layouts.app')
@section('title', 'New Investment')
@section('content')
    <div class="view-head"><div><h2>New Investment</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('investments.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Investment details</h3>
        <form method="POST" action="{{ route('investments.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Member *</label><select name="member_id" required><option value="">Select…</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }} · {{ $m->phone }}</option>@endforeach</select></div>
            <div class="field"><label>Investment product</label><select name="investment_product_id"><option value="">— None —</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} · {{ $p->return_rate }}%</option>@endforeach</select></div></div>
            <div class="form-row"><div class="field"><label>Amount *</label><input type="number" name="amount" min="1000" step="100" required></div><div class="field"><label>Return % *</label><input type="number" name="expected_return_rate" min="0" max="100" step="0.5" value="12" required></div></div>
            <div class="form-row"><div class="field"><label>Start *</label><input type="date" name="start_date" value="{{ now()->toDateString() }}" required></div><div class="field"><label>Maturity</label><input type="date" name="maturity_date" value="{{ now()->addYear()->toDateString() }}"></div></div>
            <div class="form-row"><div class="field"><label>Plan</label><input name="plan" placeholder="e.g. 12-month fixed"></div><div class="field"><label>Status *</label><select name="status"><option value="active">Active</option><option value="matured">Matured</option><option value="withdrawn">Withdrawn</option></select></div></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save investment</button>
        </form>
    </div>
@endsection
