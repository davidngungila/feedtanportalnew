@extends('layouts.app')

@section('title', 'Edit ' . $member->name)

@section('content')
    <div class="view-head">
        <div><h2>Edit member</h2><p class="sub">{{ $member->member_no }} · {{ $member->name }}</p></div>
        <div class="view-actions"><a href="{{ route('members.show', $member) }}" class="btn btn-ghost">Back to profile</a></div>
    </div>

    <div class="settings-panel">
        <h3>Member details</h3>
        <form method="POST" action="{{ route('members.update', $member) }}">
            @csrf @method('PUT')
            <div class="form-row"><div class="field"><label>Full name *</label><input name="name" value="{{ old('name', $member->name) }}" required></div><div class="field"><label>Phone *</label><input name="phone" value="{{ old('phone', $member->phone) }}" required></div></div>
            <div class="form-row"><div class="field"><label>Email</label><input name="email" value="{{ old('email', $member->email) }}"></div><div class="field"><label>National ID</label><input name="national_id" value="{{ old('national_id', $member->national_id) }}"></div></div>
            <div class="form-row"><div class="field"><label>Member type</label><select name="member_type_id"><option value="">— None —</option>@foreach($types as $t)<option value="{{ $t->id }}" {{ old('member_type_id', $member->member_type_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div><div class="field"><label>Address</label><input name="address" value="{{ old('address', $member->address) }}"></div></div>
            <div class="field"><label>Groups</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @foreach($groups as $g)<label style="display:flex;gap:6px;align-items:center;background:var(--sand-100);padding:7px 12px;border-radius:20px;font-size:13px;font-weight:600;"><input type="checkbox" name="groups[]" value="{{ $g->id }}" {{ in_array($g->id, old('groups', $member->groups->pluck('id')->toArray())) ? 'checked' : '' }}> {{ $g->name }}</label>@endforeach
                </div>
            </div>
            <div class="form-row"><div class="field"><label>Join date</label><input type="date" name="join_date" value="{{ old('join_date', $member->join_date?->format('Y-m-d')) }}"></div><div class="field"><label>Status *</label><select name="status"><option value="active" {{ $member->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $member->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3">{{ old('notes', $member->notes) }}</textarea></div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
@endsection
