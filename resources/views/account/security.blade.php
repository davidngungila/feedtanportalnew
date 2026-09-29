@extends('layouts.app')
@section('title', 'Security Setting')
@section('content')
    <div class="view-head"><div><h2>Security Setting</h2><p class="sub">Change your password and review recent access.</p></div><div class="view-actions"><a href="{{ route('account.index') }}" class="btn btn-ghost">Back to account</a></div></div>
    <div class="form-layout">
        <div class="settings-panel"><h3>Change password</h3>
            <form method="POST" action="{{ route('account.security.update') }}">@csrf @method('PUT')
                <div class="field"><label>Current password *</label><input type="password" name="current_password" required autocomplete="current-password"></div>
                <div class="form-row"><div class="field"><label>New password *</label><input type="password" name="password" required autocomplete="new-password"></div><div class="field"><label>Confirm new password *</label><input type="password" name="password_confirmation" required autocomplete="new-password"></div></div>
                <button class="btn btn-primary" type="submit">Change password</button>
            </form>
        </div>
        <div class="settings-panel"><h3>Recent access</h3>
            <div class="activity-list">
                @forelse($access as $a)<div class="activity-row"><div class="activity-text"><b>{{ ucfirst($a->event) }}</b><div class="activity-time"><span>{{ $a->created_at->format('d M Y H:i') }}</span><span>{{ $a->ip_address ?? '—' }}</span></div></div></div>
                @empty<div class="empty-state">No access records yet.</div>@endforelse
            </div>
        </div>
    </div>
@endsection
