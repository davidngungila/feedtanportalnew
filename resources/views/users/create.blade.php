@extends('layouts.app')
@section('title', 'New User')
@section('content')
    <div class="view-head"><div><h2>New User</h2><p class="sub">Individual page — no popup. Tick 1 or more roles (e.g. Loan Officer + Deposit Officer).</p></div><div class="view-actions"><a href="{{ route('users.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>User details</h3>
        <form method="POST" action="{{ route('users.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Name *</label><input name="name" required></div><div class="field"><label>Email *</label><input type="email" name="email" required></div></div>
            <div class="form-row"><div class="field"><label>Password *</label><input type="password" name="password" required></div>
            <div class="field"><label>Linked member (only for Member role)</label><select name="member_id"><option value="">— None —</option>@foreach($members as $m)<option value="{{ $m->id }}">{{ $m->name }} · {{ $m->phone }}</option>@endforeach</select></div></div>
            <div class="field"><label>Roles * (one user can hold 2+ officer roles)</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @foreach($roles as $r)<label style="display:flex;gap:6px;align-items:center;background:var(--sand-100);padding:7px 12px;border-radius:20px;font-size:13px;font-weight:600;"><input type="checkbox" name="roles[]" value="{{ $r->slug }}"> {{ $r->name }}</label>@endforeach
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Create user</button>
        </form>
    </div>
@endsection
