@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
    <div class="view-head">
        <div><h2>My profile</h2><p class="sub">{{ $member->member_no }} · {{ $member->memberType->name ?? 'Member' }} · Keep your contact details up to date.</p></div>
        <div class="view-actions"><a href="{{ route('portal.home') }}" class="btn btn-ghost">Portal</a></div>
    </div>
    <div class="panel-grid">
        <div class="panel"><div class="panel-head"><h3>Membership</h3><span class="tag {{ status_badge($member->status) }}">{{ ucfirst($member->status) }}</span></div>
            <div class="panel-body"><div class="detail-grid">
                <div class="detail-item"><div class="dk">Full name</div><div class="dv">{{ $member->name }}</div></div>
                <div class="detail-item"><div class="dk">Member no</div><div class="dv">{{ $member->member_no }}</div></div>
                <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $member->phone }}</div></div>
                <div class="detail-item"><div class="dk">Email</div><div class="dv">{{ $member->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">National ID</div><div class="dv">{{ $member->national_id ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Address</div><div class="dv">{{ $member->address ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Groups</div><div class="dv">{{ $member->groups->pluck('name')->join(', ') ?: '—' }}</div></div>
                <div class="detail-item"><div class="dk">Login email</div><div class="dv">{{ $user->email }}</div></div>
            </div></div>
        </div>
        <div class="panel"><div class="panel-head"><h3>Update contacts</h3></div>
            <div class="panel-body">
                <form method="POST" action="{{ route('portal.profile.update') }}">@csrf @method('PUT')
                    <div class="field"><label>Phone *</label><input name="phone" value="{{ old('phone', $member->phone) }}" required></div>
                    <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $member->email) }}"></div>
                    <div class="field"><label>Address</label><input name="address" value="{{ old('address', $member->address) }}"></div>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </form>
                <div class="receipt"><div class="receipt-row"><span>Password / security</span><b><a href="{{ route('account.security') }}" style="color:var(--terracotta-600);">Manage →</a></b></div></div>
            </div>
        </div>
    </div>
@endsection
