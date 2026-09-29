@extends('layouts.app')
@section('title', 'New Budget')
@section('content')
    <div class="view-head"><div><h2>New Budget</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('finance.budgets') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Budget details</h3>
        <form method="POST" action="{{ route('finance.budgets.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Account (income/expense) *</label><select name="finance_account_id" required><option value="">Select…</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }}</option>@endforeach</select></div>
            <div class="field"><label>Period</label><select name="financial_period_id"><option value="">All time</option>@foreach($periods as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div></div>
            <div class="field"><label>Budgeted amount (TZS) *</label><input type="number" name="budgeted" min="0" step="100" required></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save budget</button>
        </form>
    </div>
@endsection
