@extends('layouts.app')
@section('title', 'Edit Group')
@section('content')
    <div class="view-head"><div><h2>Edit Group</h2><p class="sub">{{ $group->name }}</p></div><div class="view-actions"><a href="{{ route('member-groups.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Group details</h3>
        <form method="POST" action="{{ route('member-groups.update', $group) }}">@csrf @method('PUT')
            <div class="field"><label>Name *</label><input name="name" value="{{ $group->name }}" required></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3">{{ $group->description }}</textarea></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active" {{ $group->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $group->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
