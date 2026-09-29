@extends('layouts.app')
@section('title', 'Edit Loan Product')
@section('content')
    <div class="view-head"><div><h2>Edit Loan Product</h2><p class="sub">{{ $product->name }}</p></div><div class="view-actions"><a href="{{ route('loan-products.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Product details</h3>
        <form method="POST" action="{{ route('loan-products.update', $product) }}">@csrf @method('PUT')
            <div class="form-row"><div class="field"><label>Name *</label><input name="name" value="{{ $product->name }}" required></div><div class="field"><label>Interest % *</label><input type="number" name="interest_rate" value="{{ $product->interest_rate }}" min="0" max="100" step="0.5" required></div></div>
            <div class="form-row"><div class="field"><label>Min amount *</label><input type="number" name="min_amount" value="{{ $product->min_amount }}" required></div><div class="field"><label>Max amount *</label><input type="number" name="max_amount" value="{{ $product->max_amount }}" required></div></div>
            <div class="form-row"><div class="field"><label>Duration (months) *</label><input type="number" name="duration_months" value="{{ $product->duration_months }}" required></div><div class="field"><label>Status *</label><select name="status"><option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3">{{ $product->description }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
