@extends('layouts.app')
@section('title', 'New Role')
@section('content')
    <div class="view-head"><div><h2>New Role</h2><p class="sub">Individual page — no popup. Slug is lowercase with underscores.</p></div><div class="view-actions"><a href="{{ route('roles.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Role details</h3>
        <form method="POST" action="{{ route('roles.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Slug *</label><input name="slug" required placeholder="e.g. audit_officer"></div><div class="field"><label>Name *</label><input name="name" required placeholder="e.g. Audit Officer"></div></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Save role</button>
        </form>
    </div>
@endsection
