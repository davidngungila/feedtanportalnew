@extends('layouts.app')
@section('title', 'New Receivable / Payable')
@section('content')
    <div class="view-head"><div><h2>New {{ $kind === 'payable' ? 'Payable' : 'Receivable' }}</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ $kind === 'payable' ? route('finance.payables') : route('finance.receivables') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Details</h3>
        <form method="POST" action="{{ route('finance.receivables.store') }}">@csrf
            <input type="hidden" name="kind" value="{{ $kind }}">
            <div class="form-row"><div class="field"><label>Party name *</label><input name="party_name" required placeholder="e.g. Supplier / Customer"></div><div class="field"><label>Member (optional)</label><select name="member_id"><option value="">— None —</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach</select></div></div>
            <div class="form-row"><div class="field"><label>Amount (TZS) *</label><input type="number" name="amount" min="100" step="100" required></div><div class="field"><label>Due date</label><input type="date" name="due_date"></div></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </div>
@endsection
