@extends('layouts.app')
@section('title', 'New Account')
@section('content')
    <div class="view-head"><div><h2>New Account</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('finance.chart') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Account details</h3>
        <form method="POST" action="{{ route('finance.accounts.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Code *</label><input name="code" required placeholder="e.g. 5300"></div><div class="field"><label>Name *</label><input name="name" required placeholder="e.g. Transport"></div></div>
            <div class="form-row"><div class="field"><label>Type *</label><select name="type"><option value="asset">Asset</option><option value="liability">Liability</option><option value="equity">Equity</option><option value="income">Income</option><option value="expense">Expense</option></select></div><div class="field"><label>Opening balance</label><input type="number" name="opening_balance" min="0" step="100" value="0"></div></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save account</button>
        </form>
    </div>
@endsection
