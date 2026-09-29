@extends('layouts.app')
@section('title', 'New Investment Product')
@section('content')
    <div class="view-head"><div><h2>New Investment Product</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('investment-products.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Product details</h3>
        <form method="POST" action="{{ route('investment-products.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Name *</label><input name="name" required placeholder="e.g. 12-month Fixed"></div><div class="field"><label>Return % *</label><input type="number" name="return_rate" min="0" max="100" step="0.5" value="12" required></div></div>
            <div class="form-row"><div class="field"><label>Min amount *</label><input type="number" name="min_amount" min="0" step="100" value="1000" required></div><div class="field"><label>Duration (months) *</label><input type="number" name="duration_months" min="1" max="120" value="12" required></div></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save product</button>
        </form>
    </div>
@endsection
