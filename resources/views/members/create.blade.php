@extends('layouts.app')

@section('title', 'Member Registration')

@section('content')
    <div class="view-head">
        <div><h2>Member Registration</h2><p class="sub">Register a new member on its own page.</p></div>
        <div class="view-actions"><a href="{{ route('members.index') }}" class="btn btn-ghost">Back to members</a></div>
    </div>

    <div class="form-layout">
        <div class="settings-panel">
            <h3>Member details</h3>
            <form method="POST" action="{{ route('members.store') }}">
                @csrf
                <div class="form-row">
                    <div class="field"><label>Full name *</label><input name="name" value="{{ old('name') }}" required placeholder="e.g. Amina Juma"></div>
                    <div class="field"><label>Phone *</label><input name="phone" value="{{ old('phone') }}" required placeholder="07xx xxx xxx"></div>
                </div>
                <div class="form-row">
                    <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" placeholder="member@example.com"></div>
                    <div class="field"><label>National ID</label><input name="national_id" value="{{ old('national_id') }}" placeholder="NIDA no"></div>
                </div>
                <div class="form-row">
                    <div class="field"><label>Member type</label><select name="member_type_id"><option value="">— None —</option>@foreach($types as $t)<option value="{{ $t->id }}" {{ old('member_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Address</label><input name="address" value="{{ old('address') }}" placeholder="Ward / Street"></div>
                </div>
                <div class="field"><label>Groups</label>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        @foreach($groups as $g)<label style="display:flex;gap:6px;align-items:center;background:var(--sand-100);padding:7px 12px;border-radius:20px;font-size:13px;font-weight:600;"><input type="checkbox" name="groups[]" value="{{ $g->id }}"> {{ $g->name }}</label>@endforeach
                    </div>
                </div>
                <div class="form-row">
                    <div class="field"><label>Join date</label><input type="date" name="join_date" value="{{ old('join_date', now()->toDateString()) }}"></div>
                    <div class="field"><label>Status *</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
                <div class="field"><label>Notes</label><textarea name="notes" rows="3" placeholder="Optional notes">{{ old('notes') }}</textarea></div>
                <button class="btn btn-primary" type="submit">Save member</button>
            </form>
        </div>
        <div class="settings-panel">
            <h3>What happens next</h3>
            <div class="detail-grid">
                <div class="detail-item"><div class="dk">Step 1</div><div class="dv">Member gets a unique member number automatically.</div></div>
                <div class="detail-item"><div class="dk">Step 2</div><div class="dv">Record deposits, loans, investments and SWF per member.</div></div>
                <div class="detail-item"><div class="dk">Step 3</div><div class="dv">Attach documents under Member Documents.</div></div>
            </div>
        </div>
    </div>
@endsection
