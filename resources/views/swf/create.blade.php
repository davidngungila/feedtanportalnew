@extends('layouts.app')
@section('title', 'New SWF Entry')
@section('content')
    <div class="view-head"><div><h2>New SWF Entry</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('swf.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Entry details</h3>
        <form method="POST" action="{{ route('swf.store') }}">@csrf
            <div class="field"><label>Member *</label><select name="member_id" required><option value="">Select…</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }} · {{ $m->phone }}</option>@endforeach</select></div>
            <div class="form-row"><div class="field"><label>Type *</label><select name="type"><option value="contribution">Contribution</option><option value="deduction">Deduction</option><option value="payout">Payout</option><option value="claim">Claim</option></select></div><div class="field"><label>Amount *</label><input type="number" name="amount" min="100" step="100" required></div></div>
            <div class="form-row"><div class="field"><label>Method *</label><select name="method"><option value="cash">Cash</option><option value="mobile">Mobile</option><option value="bank">Bank</option></select></div><div class="field"><label>Date *</label><input type="date" name="transacted_at" value="{{ now()->toDateString() }}" required></div></div>
            <div class="field"><label>Reason</label><input name="reason" placeholder="e.g. Monthly contribution / Medical claim"></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save entry</button>
        </form>
    </div>
@endsection
