@extends('layouts.app')
@section('title', 'Membership · Step '.$step)
@section('content')
    <div class="view-head">
        <div><h2>Become a member — step {{ $step }} of 4</h2><p class="sub">{{ $steps[$step] }} · your progress saves automatically.</p></div>
    </div>

    <div class="tabs" style="margin-bottom:24px;">
        @foreach($steps as $n => $label)
        <a href="{{ $n <= $app->current_step ? route('join.step', $n) : '#' }}" class="tab-btn {{ $step === $n ? 'active' : '' }}" @if($n > $app->current_step) onclick="return false" style="opacity:.5;" @endif>
            @if($n < $app->current_step) ✓ @else {{ $n }} · @endif {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="form-layout">
        <div class="settings-panel">
            @if($step === 1)
            <h3>Personal details</h3>
            <form method="POST" action="{{ route('join.save', 1) }}">@csrf
                <div class="field"><label>Full name *</label><input name="name" value="{{ old('name', $app->name) }}" required></div>
                <div class="form-row">
                    <div class="field"><label>Phone *</label><input name="phone" value="{{ old('phone', $app->phone) }}" required></div>
                    <div class="field"><label>National ID</label><input name="national_id" value="{{ old('national_id', $app->national_id) }}"></div>
                </div>
                <button class="btn btn-primary" type="submit">Save &amp; continue →</button>
            </form>
            @elseif($step === 2)
            <h3>Contact</h3>
            <form method="POST" action="{{ route('join.save', 2) }}">@csrf
                <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $app->email) }}"></div>
                <div class="field"><label>Address</label><input name="address" value="{{ old('address', $app->address) }}" placeholder="Street, ward, district"></div>
                <div style="display:flex;gap:10px;"><a href="{{ route('join.step', 1) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; continue →</button></div>
            </form>
            @elseif($step === 3)
            <h3>Membership</h3>
            <form method="POST" action="{{ route('join.save', 3) }}">@csrf
                <div class="form-row">
                    <div class="field"><label>Member type</label><select name="member_type_id"><option value="">— Select —</option>@foreach($types as $t)<option value="{{ $t->id }}" {{ (string)old('member_type_id', $app->member_type_id) === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Group</label><select name="member_group_id"><option value="">— None —</option>@foreach($groups as $g)<option value="{{ $g->id }}" {{ (string)old('member_group_id', $app->member_group_id) === (string)$g->id ? 'selected' : '' }}>{{ $g->name }}</option>@endforeach</select></div>
                </div>
                <div class="field"><label>Notes</label><textarea name="notes" rows="3" placeholder="Anything the office should know">{{ old('notes', $app->notes) }}</textarea></div>
                <div style="display:flex;gap:10px;"><a href="{{ route('join.step', 2) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; review →</button></div>
            </form>
            @else
            <h3>Review &amp; submit</h3>
            <div class="detail-grid">
                <div class="detail-item"><div class="dk">Name</div><div class="dv">{{ $app->name }}</div></div>
                <div class="detail-item"><div class="dk">Phone</div><div class="dv">{{ $app->phone }}</div></div>
                <div class="detail-item"><div class="dk">National ID</div><div class="dv">{{ $app->national_id ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Email</div><div class="dv">{{ $app->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Address</div><div class="dv">{{ $app->address ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Type</div><div class="dv">{{ $app->memberType->name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Group</div><div class="dv">{{ $app->memberGroup->name ?? '—' }}</div></div>
            </div>
            <form method="POST" action="{{ route('join.submit') }}" style="display:flex;gap:10px;margin-top:18px;">@csrf
                <a href="{{ route('join.step', 3) }}" class="btn btn-ghost">← Back</a>
                <button class="btn btn-primary" type="submit">Submit application</button>
            </form>
            @endif
        </div>
        <div class="settings-panel"><h3>How it works</h3>
            <div class="toggle-row"><div class="toggle-text"><strong>1–3 · Your details</strong><span>Saved as you go — come back anytime.</span></div></div>
            <div class="toggle-row"><div class="toggle-text"><strong>4 · Office review</strong><span>Approved accounts unlock loans, savings, investments and SWF.</span></div></div>
        </div>
    </div>
@endsection
