@extends('layouts.app')
@section('title', 'New Financial Period')
@section('content')
    <div class="view-head"><div><h2>New Financial Period</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('finance.periods') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Period details</h3>
        <form method="POST" action="{{ route('finance.periods.store') }}">@csrf
            <div class="field"><label>Name *</label><input name="name" required placeholder="e.g. FY 2026 Q1"></div>
            <div class="form-row"><div class="field"><label>Starts *</label><input type="date" name="starts_at" required></div><div class="field"><label>Ends *</label><input type="date" name="ends_at" required></div></div>
            <div class="field"><label>Status *</label><select name="status"><option value="open">Open</option><option value="closed">Closed</option></select></div>
            <button class="btn btn-primary" type="submit">Save period</button>
        </form>
    </div>
@endsection
