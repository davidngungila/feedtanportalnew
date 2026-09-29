@extends('layouts.app')
@section('title', 'New Transfer')
@section('content')
    <div class="view-head"><div><h2>New Transfer</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('finance.transfers') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Transfer details</h3>
        <form method="POST" action="{{ route('finance.transfers.store') }}">@csrf
            <div class="form-row"><div class="field"><label>From account *</label><select name="from_account_id" required><option value="">Select…</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }} (@money($a->balance()))</option>@endforeach</select></div>
            <div class="field"><label>To account *</label><select name="to_account_id" required><option value="">Select…</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} · {{ $a->name }}</option>@endforeach</select></div></div>
            <div class="form-row"><div class="field"><label>Amount (TZS) *</label><input type="number" name="amount" min="100" step="100" required></div><div class="field"><label>Date *</label><input type="date" name="transferred_at" value="{{ now()->toDateString() }}" required></div></div>
            <div class="field"><label>Notes</label><input name="notes" placeholder="e.g. Cash to bank"></div>
            <button class="btn btn-primary" type="submit">Post transfer</button>
        </form>
    </div>
@endsection
