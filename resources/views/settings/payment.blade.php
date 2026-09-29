@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="view-head"><div><h2>{{ $title }}</h2><p class="sub">{{ $sub }}</p></div><div class="view-actions"><a href="{{ route('settings.index') }}" class="btn btn-ghost">General</a></div></div>
    <div class="settings-panel"><h3>Payout accounts</h3>
        <form method="POST" action="{{ route('settings.payment.update') }}">@csrf @method('PUT')
            <div class="form-row"><div class="field"><label>Default method</label><select name="pay_default_method"><option value="">—</option>@foreach(['cash','mobile','bank'] as $m)<option value="{{ $m }}" {{ ($settings['pay_default_method'] ?? '') === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>@endforeach</select></div>
            <div class="field"><label>Approval above (TZS)</label><input type="number" name="pay_require_approval_above" min="0" step="100" value="{{ $settings['pay_require_approval_above'] ?? '' }}"></div></div>
            <div class="form-row"><div class="field"><label>M-Pesa number</label><input name="pay_mpesa_number" value="{{ $settings['pay_mpesa_number'] ?? '' }}"></div><div class="field"><label>Tigo number</label><input name="pay_tigo_number" value="{{ $settings['pay_tigo_number'] ?? '' }}"></div></div>
            <div class="field"><label>Airtel number</label><input name="pay_airtel_number" value="{{ $settings['pay_airtel_number'] ?? '' }}"></div>
            <div class="form-row"><div class="field"><label>Bank name</label><input name="pay_bank_name" value="{{ $settings['pay_bank_name'] ?? '' }}"></div><div class="field"><label>Bank account</label><input name="pay_bank_account" value="{{ $settings['pay_bank_account'] ?? '' }}"></div></div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </div>
@endsection
