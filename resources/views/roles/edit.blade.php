@extends('layouts.app')
@section('title', 'Edit Role')
@section('content')
    <div class="view-head"><div><h2>Edit Role</h2><p class="sub">{{ $role->name }} · {{ $role->slug }}</p></div><div class="view-actions"><a href="{{ route('roles.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Role details</h3>
        <form method="POST" action="{{ route('roles.update', $role) }}">@csrf @method('PUT')
            <div class="field"><label>Name *</label><input name="name" value="{{ $role->name }}" required></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3">{{ $role->description }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
