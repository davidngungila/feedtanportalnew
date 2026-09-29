@extends('layouts.app')
@section('title', 'Edit Member Type')
@section('content')
    <div class="view-head"><div><h2>Edit Member Type</h2><p class="sub">{{ $type->name }}</p></div><div class="view-actions"><a href="{{ route('member-types.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Type details</h3>
        <form method="POST" action="{{ route('member-types.update', $type) }}">@csrf @method('PUT')
            <div class="field"><label>Name *</label><input name="name" value="{{ $type->name }}" required></div>
            <div class="field"><label>Description</label><textarea name="description" rows="3">{{ $type->description }}</textarea></div>
            <div class="field"><label>Status *</label><select name="status"><option value="active" {{ $type->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $type->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
