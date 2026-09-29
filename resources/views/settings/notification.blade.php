@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="view-head"><div><h2>{{ $title }}</h2><p class="sub">{{ $sub }}</p></div><div class="view-actions"><a href="{{ route('settings.index') }}" class="btn btn-ghost">General</a></div></div>
    <div class="settings-panel"><h3>Alert toggles</h3>
        <form method="POST" action="{{ route('settings.notification.update') }}">@csrf @method('PUT')
            @foreach(['notify_loan_due' => 'Loan due reminders', 'notify_overdue' => 'Overdue alerts', 'notify_matured_investment' => 'Matured investment alerts', 'notify_new_application' => 'New application alerts', 'notify_daily_summary' => 'Daily summary'] as $key => $label)
            <div class="toggle-row"><div class="toggle-text"><strong>{{ $label }}</strong></div>
                <select name="{{ $key }}" style="padding:8px 12px;border:1.5px solid var(--line);border-radius:9px;font-weight:600;"><option value="1" {{ ($settings[$key] ?? '1') === '1' ? 'selected' : '' }}>On</option><option value="0" {{ ($settings[$key] ?? '1') === '0' ? 'selected' : '' }}>Off</option></select>
            </div>
            @endforeach
            <div style="margin-top:16px;"><button class="btn btn-primary" type="submit">Save</button></div>
        </form>
    </div>
@endsection
