@extends('layouts.app')
@section('title', 'New Member Type')
@section('content')
    <div class="view-head"><div><h2>New Member Type</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('member-types.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Type details</h3>
        <form method="POST" action="{{ route('member-types.store') }}">@csrf
            <div class="field"><label>Name *</label><input name="name" required placeholder="e.g. Ordinary"></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <button class="btn btn-primary" type="submit">Save type</button>
        </form>
    </div>
@endsection
