@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="view-head"><div><h2>Settings</h2><p class="sub">Defaults used when recording member funds.</p></div></div>

    <div class="form-layout">
        <div class="settings-panel">
            <h3>Fund defaults</h3>
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf @method('PUT')
                <div class="field"><label>Organisation name</label><input name="org_name" value="{{ $settings['org_name'] ?? 'Feedtan Portal' }}"></div>
                <div class="field"><label>Default loan interest %</label><input type="number" step="0.5" min="0" max="100" name="default_interest_rate" value="{{ $settings['default_interest_rate'] ?? '10' }}"></div>
                <div class="field"><label>Default investment return %</label><input type="number" step="0.5" min="0" max="100" name="default_investment_return" value="{{ $settings['default_investment_return'] ?? '12' }}"></div>
                <div class="field"><label>SWF monthly amount (TZS)</label><input type="number" step="100" min="0" name="swf_monthly_amount" value="{{ $settings['swf_monthly_amount'] ?? '5000' }}"></div>
                <button class="btn btn-primary" type="submit">Save settings</button>
            </form>
        </div>
        <div class="settings-panel">
            <h3>How it works</h3>
            <div class="detail-grid">
                <div class="detail-item"><div class="dk">Members</div><div class="dv">One record per member, with phone as unique ID.</div></div>
                <div class="detail-item"><div class="dk">Loans</div><div class="dv">Principal + % interest = payable. Repayments reduce outstanding.</div></div>
                <div class="detail-item"><div class="dk">Deposits</div><div class="dv">Savings in minus out = net balance.</div></div>
                <div class="detail-item"><div class="dk">Investments</div><div class="dv">Fixed plans with expected return and maturity.</div></div>
                <div class="detail-item"><div class="dk">SWF</div><div class="dv">Social Welfare Fund: contributions minus payouts.</div></div>
            </div>
            <div class="receipt"><div class="receipt-row"><span>Blade base</span><b>Copied from Wakala system</b></div><div class="receipt-row"><span>Adapted for</span><b>Member funds</b></div></div>
        </div>
    </div>
@endsection
