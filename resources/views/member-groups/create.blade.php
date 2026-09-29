@extends('layouts.app')
@section('title', 'New Member Group')
@section('content')
    <div class="view-head"><div><h2>New Member Group</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('member-groups.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Group details</h3>
        <form method="POST" action="{{ route('member-groups.store') }}">@csrf
            <div class="field"><label>Name *</label><input name="name" required placeholder="e.g. Ward A Circle"></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <button class="btn btn-primary" type="submit">Save group</button>
        </form>
    </div>
@endsection
