@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="view-head"><div><h2>{{ $title }}</h2><p class="sub">{{ $sub }}</p></div><div class="view-actions"><a href="{{ route('settings.index') }}" class="btn btn-ghost">General</a></div></div>
    <div class="settings-panel"><h3>Organization</h3>
        <form method="POST" action="{{ route('settings.organization.update') }}">@csrf @method('PUT')
            <div class="form-row"><div class="field"><label>Organisation name</label><input name="org_name" value="{{ $settings['org_name'] ?? '' }}"></div><div class="field"><label>Short name</label><input name="org_short_name" value="{{ $settings['org_short_name'] ?? '' }}"></div></div>
            <div class="form-row"><div class="field"><label>Phone</label><input name="org_phone" value="{{ $settings['org_phone'] ?? '' }}"></div><div class="field"><label>Email</label><input type="email" name="org_email" value="{{ $settings['org_email'] ?? '' }}"></div></div>
            <div class="field"><label>Address</label><input name="org_address" value="{{ $settings['org_address'] ?? '' }}"></div>
            <div class="field"><label>Registration no</label><input name="org_registration_no" value="{{ $settings['org_registration_no'] ?? '' }}"></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </div>
@endsection
