@extends('layouts.app')
@section('title', 'New Application')
@section('content')
    <div class="view-head"><div><h2>New Membership Application</h2><p class="sub">Individual page — no popup.</p></div><div class="view-actions"><a href="{{ route('member-applications.index') }}" class="btn btn-ghost">Back</a></div></div>
    <div class="settings-panel"><h3>Applicant details</h3>
        <form method="POST" action="{{ route('member-applications.store') }}">@csrf
            <div class="form-row"><div class="field"><label>Full name *</label><input name="name" required></div><div class="field"><label>Phone *</label><input name="phone" required></div></div>
            <div class="form-row"><div class="field"><label>Email</label><input type="email" name="email"></div><div class="field"><label>National ID</label><input name="national_id"></div></div>
            <div class="field"><label>Address</label><input name="address"></div>
            <div class="form-row"><div class="field"><label>Member type</label><select name="member_type_id"><option value="">— None —</option>@foreach($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div><div class="field"><label>Group</label><select name="member_group_id"><option value="">— None —</option>@foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach</select></div></div>
            <div class="field"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
            <button class="btn btn-primary" type="submit">Submit application</button>
        </form>
    </div>
@endsection
