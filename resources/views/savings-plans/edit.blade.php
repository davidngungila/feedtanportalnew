@extends('layouts.app')
@section('title', 'Edit Savings Plan')
@section('content')
    <div class="view-head"><div><h2>Edit Savings Plan</h2><p class="sub">{{ $plan->name }}</p></div><div class="view-actions"><a href="{{ route('savings-plans.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Plan details</h3>
        <form method="POST" action="{{ route('savings-plans.update', $plan) }}">@csrf @method('PUT')
            <div class="form-row"><div class="field"><label>Name *</label><input name="name" value="{{ $plan->name }}" required></div><div class="field"><label>Target amount *</label><input type="number" name="target_amount" value="{{ $plan->target_amount }}" required></div></div>
            <div class="form-row"><div class="field"><label>Duration (months) *</label><input type="number" name="duration_months" value="{{ $plan->duration_months }}" required></div><div class="field"><label>Deposit product</label><select name="deposit_product_id"><option value="">— None —</option>@foreach($products as $p)<option value="{{ $p->id }}" {{ $plan->deposit_product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach</select></div></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active" {{ $plan->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $plan->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3">{{ $plan->description }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
