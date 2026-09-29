@extends('layouts.app')
@section('title', 'New Reconciliation')
@section('content')
    <div class="view-head"><div><h2>New Reconciliation</h2><p class="sub">System balance is computed from the ledger automatically.</p></div><div class="view-actions"><a href="{{ route('finance.reconciliation') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Reconciliation details</h3>
        <form method="POST" action="{{ route('finance.reconciliation.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Account (asset/liability) *</label><select name="finance_account_id" required><option value="">Select…</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }} (@money($a->balance()))</option>@endforeach</select></div>
            <div class="field"><label>Statement date *</label><input type="date" name="statement_date" value="{{ now()->toDateString() }}" required></div></div>
            <div class="field"><label>Statement balance (TZS) *</label><input type="number" name="statement_balance" step="0.01" required></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Reconcile</button>
        </form>
    </div>
@endsection
