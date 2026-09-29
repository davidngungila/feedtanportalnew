@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="view-head"><div><h2>{{ $title }}</h2><p class="sub">{{ $sub }}</p></div><div class="view-actions"><a href="{{ route('settings.index') }}" class="btn btn-ghost">General</a></div></div>
    <div class="settings-panel"><h3>System</h3>
        <form method="POST" action="{{ route('settings.system.update') }}">@csrf @method('PUT')
            <div class="form-row"><div class="field"><label>Session lifetime (minutes)</label><input type="number" name="sys_session_lifetime" min="15" max="1440" value="{{ $settings['sys_session_lifetime'] ?? '120' }}"></div>
            <div class="field"><label>Activity retention (days)</label><input type="number" name="sys_activity_retention_days" min="30" max="3650" value="{{ $settings['sys_activity_retention_days'] ?? '365' }}"></div></div>
            <div class="form-row"><div class="field"><label>Currency</label><input name="sys_currency" value="{{ $settings['sys_currency'] ?? 'TZS' }}"></div>
            <div class="field"><label>Maintenance mode</label><select name="sys_maintenance_mode"><option value="0" {{ ($settings['sys_maintenance_mode'] ?? '0') === '0' ? 'selected' : '' }}>Off</option><option value="1" {{ ($settings['sys_maintenance_mode'] ?? '0') === '1' ? 'selected' : '' }}>On</option></select></div></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </div>
@endsection
