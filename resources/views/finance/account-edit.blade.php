@extends('layouts.app')
@section('title', 'Edit Account')
@section('content')
    <div class="view-head"><div><h2>Edit Account</h2><p class="sub">{{ $account->code }} · {{ $account->name }}</p></div><div class="view-actions"><a href="{{ route('finance.chart') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Account details</h3>
        <form method="POST" action="{{ route('finance.accounts.update', $account) }}">@csrf @method('PUT')
            <div class="field"><label>Name *</label><input name="name" value="{{ $account->name }}" required></div>
            <div class="field"><label>Opening balance</label><input type="number" name="opening_balance" value="{{ $account->opening_balance }}" min="0" step="100"></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active" {{ $account->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $account->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3">{{ $account->description }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
