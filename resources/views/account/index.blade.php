@extends('layouts.app')
@section('title', 'My Account')
@section('content')
    <div class="view-head">
        <div><h2>My Account</h2><p class="sub">{{ $user->email }} · Member since {{ $user->created_at->format('d M Y') }}</p></div>
        <div class="view-actions"><a href="{{ route('account.security') }}" class="btn btn-primary">Security setting</a></div>
    </div>

    <div class="panel">
        <div class="panel-body" style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
            <div class="avatar {{ $user->hasRole('administrator') ? 'gold' : ($user->hasRole('chairperson', 'accountant') ? 'acacia' : '') }}" style="width:64px;height:64px;font-size:22px;">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div style="flex:1;min-width:220px;">
                <h3 style="font-size:20px;">{{ $user->name }}</h3>
                <p class="sub" style="margin:4px 0 8px;">{{ $user->email }}</p>
                <div><span class="tag tag-gold">{{ $user->primaryRoleLabel() }}</span></div>
                <div style="margin-top:8px;">@foreach($user->roles as $r)<span class="tag tag-green" style="margin:0 4px 4px 0;">{{ $r->name }}</span>@endforeach</div>
            </div>
            <div class="detail-grid" style="min-width:240px;flex:1;">
                <div class="detail-item"><div class="dk">Last login</div><div class="dv">{{ $lastLogin ? $lastLogin->created_at->format('d M Y H:i') : '—' }}</div></div>
                <div class="detail-item"><div class="dk">Linked member</div><div class="dv">{{ $user->member->name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Actions logged</div><div class="dv">{{ $activity->count() }} recent</div></div>
            </div>
        </div>
    </div>

    @if($user->member && $memberBalance)
    <div class="balance-strip">
        <div class="balance-box"><div class="bb-label">My savings</div><div class="bb-amount">@money($memberBalance['savings'])</div></div>
        <div class="balance-box"><div class="bb-label">My loans outstanding</div><div class="bb-amount">@money($memberBalance['loan_outstanding'])</div></div>
        <div class="balance-box"><div class="bb-label">My investments</div><div class="bb-amount">@money($memberBalance['invested'])</div></div>
        <div class="balance-box"><div class="bb-label">My SWF</div><div class="bb-amount">@money($memberBalance['swf'])</div><div class="bb-sub"><a href="{{ route('members.show', $user->member) }}">Open my member profile →</a></div></div>
    </div>
    @endif

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head"><h3>Edit profile</h3></div>
            <div class="panel-body">
                <form method="POST" action="{{ route('account.update') }}">@csrf @method('PUT')
                    <div class="form-row"><div class="field"><label>Name *</label><input name="name" value="{{ old('name', $user->name) }}" required></div><div class="field"><label>Email *</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required></div></div>
                    <div class="receipt"><div class="receipt-row"><span>Header role</span><b>{{ $user->primaryRoleLabel() }}</b></div><div class="receipt-row"><span>Role changes</span><b>Managed by an administrator</b></div></div>
                    <div style="margin-top:14px;"><button class="btn btn-primary" type="submit">Save changes</button></div>
                </form>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><h3>My recent activity</h3><span class="link">{{ $activity->count() }}</span></div>
            <div class="panel-body"><div class="activity-list">
                @forelse($activity as $a)<div class="activity-row"><div class="activity-text"><b>{{ $a->action }}</b><div class="activity-time"><span>{{ $a->created_at->diffForHumans() }}</span><span>{{ $a->ip_address ?? '' }}</span></div></div></div>
                @empty<div class="empty-state">No activity recorded yet.</div>@endforelse
            </div></div>
        </div>
    </div>
@endsection
