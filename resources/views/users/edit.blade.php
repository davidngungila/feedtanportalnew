@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
    <div class="view-head"><div><h2>Edit User</h2><p class="sub">{{ $user->name }} · {{ $user->email }}</p></div><div class="view-actions"><a href="{{ route('users.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>User details</h3>
        @php $mine = $user->roles->pluck('slug')->toArray(); @endphp
        <form method="POST" action="{{ route('users.update', $user) }}">@csrf @method('PUT')
            <div class="form-row"><div class="field"><label>Name *</label><input name="name" value="{{ $user->name }}" required></div><div class="field"><label>Email *</label><input type="email" name="email" value="{{ $user->email }}" required></div></div>
            <div class="form-row"><div class="field"><label>Password (leave blank to keep)</label><input type="password" name="password"></div>
            <div class="field"><label>Linked member</label><select name="member_id"><option value="">— None —</option>@foreach($members as $m)<option value="{{ $m->id }}" {{ (string)$user->member_id === (string)$m->id ? 'selected' : '' }}>{{ $m->name }}</option>@endforeach</select></div></div>
            <div class="field"><label>Roles *</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @foreach($roles as $r)<label style="display:flex;gap:6px;align-items:center;background:var(--sand-100);padding:7px 12px;border-radius:20px;font-size:13px;font-weight:600;"><input type="checkbox" name="roles[]" value="{{ $r->slug }}" {{ in_array($r->slug, $mine) ? 'checked' : '' }}> {{ $r->name }}</label>@endforeach
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
