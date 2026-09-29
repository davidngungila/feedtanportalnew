@extends('layouts.app')
@section('title', 'New Savings Plan')
@section('content')
    <div class="view-head"><div><h2>New Savings Plan</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('savings-plans.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Plan details</h3>
        <form method="POST" action="{{ route('savings-plans.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Name *</label><input name="name" required placeholder="e.g. Holiday 12-month"></div><div class="field"><label>Target amount *</label><input type="number" name="target_amount" min="0" step="100" value="100000" required></div></div>
            <div class="form-row"><div class="field"><label>Duration (months) *</label><input type="number" name="duration_months" min="1" max="120" value="12" required></div><div class="field"><label>Deposit product</label><select name="deposit_product_id"><option value="">— None —</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save plan</button>
        </form>
    </div>
@endsection
