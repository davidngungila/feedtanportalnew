@extends('layouts.app')
@section('title', 'Membership · Step '.$step)
@section('content')
    <div class="view-head">
        <div><h2>Become a member — step {{ $step }} of 4</h2><p class="sub">{{ $steps[$step] }} · your progress saves automatically after each step.</p></div>
        <div class="view-actions"><a href="{{ route('join.status') }}" class="btn btn-ghost">Application status</a></div>
    </div>

    <div class="settings-panel" style="max-width:780px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;"><span class="tag tag-gold">Step {{ $step }} of 4</span><span class="cell-sub">Application ref #{{ $app->id }}</span></div>
            @if($step === 1)
            <h3>Personal details</h3>
            <p style="font-size:13.5px;color:var(--ink-soft);margin-bottom:18px;line-height:1.7;">Tell us who you are. Your name must match your national ID — the office verifies this before approval, and your phone receives SMS notifications about loans, payouts and approvals.</p>
            <form method="POST" action="{{ route('join.save', eid(1)) }}">@csrf
                <div class="field"><label>Full name *</label><input name="name" value="{{ old('name', $app->name) }}" placeholder="As shown on your national ID" required></div>
                <div class="form-row">
                    <div class="field"><label>Phone *</label><input name="phone" value="{{ old('phone', $app->phone) }}" placeholder="07…" required></div>
                    <div class="field"><label>National ID</label><input name="national_id" value="{{ old('national_id', $app->national_id) }}" placeholder="e.g. 1990-01-01-0000-00001-01"></div>
                </div>
                <button class="btn btn-primary" type="submit">Save &amp; continue →</button>
            </form>
            @elseif($step === 2)
            <h3>Contact</h3>
            <p style="font-size:13.5px;color:var(--ink-soft);margin-bottom:18px;line-height:1.7;">Where do we reach you? Approval decisions, verification codes and statements go to these contacts — double-check they are correct.</p>
            <form method="POST" action="{{ route('join.save', eid(2)) }}">@csrf
                <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $app->email) }}" placeholder="you@example.com"></div>
                <div class="field"><label>Address</label><input name="address" value="{{ old('address', $app->address) }}" placeholder="Street, ward, district"></div>
                <div style="display:flex;gap:10px;"><a href="{{ route('join.step', eid(1)) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; continue →</button></div>
            </form>
            @elseif($step === 3)
            <h3>Membership</h3>
            <p style="font-size:13.5px;color:var(--ink-soft);margin-bottom:18px;line-height:1.7;">Choose the type that fits you and optionally a group. Types decide your contribution rules; groups organize members that save or borrow together.</p>
            <form method="POST" action="{{ route('join.save', eid(3)) }}">@csrf
                <div class="form-row">
                    <div class="field"><label>Member type</label><select name="member_type_id"><option value="">— Select —</option>@foreach($types as $t)<option value="{{ $t->id }}" {{ (string)old('member_type_id', $app->member_type_id) === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Group</label><select name="member_group_id"><option value="">— None —</option>@foreach($groups as $g)<option value="{{ $g->id }}" {{ (string)old('member_group_id', $app->member_group_id) === (string)$g->id ? 'selected' : '' }}>{{ $g->name }}</option>@endforeach</select></div>
                </div>
                <div class="field"><label>Notes</label><textarea name="notes" rows="3" placeholder="Anything the office should know">{{ old('notes', $app->notes) }}</textarea></div>
                <div style="display:flex;gap:10px;"><a href="{{ route('join.step', eid(2)) }}" class="btn btn-ghost">← Back</a><button class="btn btn-primary" type="submit">Save &amp; review →</button></div>
            </form>
            @if($types->isNotEmpty() || $groups->isNotEmpty())
            <div class="receipt">
                @foreach($types as $t)<div class="receipt-row"><span>{{ $t->name }}</span><b style="font-weight:600;">{{ $t->description ?? 'Member type' }}</b></div>@endforeach
                @foreach($groups as $g)<div class="receipt-row"><span>{{ $g->name }} (group)</span><b style="font-weight:600;">{{ $g->description ?? 'Member group' }}</b></div>@endforeach
            </div>
            @endif
            @else
            <h3>Review &amp; submit</h3>
            <p style="font-size:13.5px;color:var(--ink-soft);margin-bottom:18px;line-height:1.7;">Check everything once more. You can jump back to any step to fix it — nothing is sent until you press submit.</p>
            <div class="detail-grid">
                <div class="detail-item"><div class="dk">Name <a href="{{ route('join.step', eid(1)) }}" style="color:var(--terracotta-600);">Edit</a></div><div class="dv">{{ $app->name }}</div></div>
                <div class="detail-item"><div class="dk">Phone <a href="{{ route('join.step', eid(1)) }}" style="color:var(--terracotta-600);">Edit</a></div><div class="dv">{{ $app->phone }}</div></div>
                <div class="detail-item"><div class="dk">National ID</div><div class="dv">{{ $app->national_id ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Email <a href="{{ route('join.step', eid(2)) }}" style="color:var(--terracotta-600);">Edit</a></div><div class="dv">{{ $app->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Address</div><div class="dv">{{ $app->address ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Type <a href="{{ route('join.step', eid(3)) }}" style="color:var(--terracotta-600);">Edit</a></div><div class="dv">{{ $app->memberType->name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Group</div><div class="dv">{{ $app->memberGroup->name ?? '—' }}</div></div>
                <div class="detail-item"><div class="dk">Notes</div><div class="dv">{{ $app->notes ?? '—' }}</div></div>
            </div>
            <form method="POST" action="{{ route('join.submit') }}" style="display:flex;gap:10px;margin-top:18px;">@csrf
                <a href="{{ route('join.step', eid(3)) }}" class="btn btn-ghost">← Back</a>
                <button class="btn btn-primary" type="submit">Submit application</button>
            </form>
            @endif
    </div>
@endsection
