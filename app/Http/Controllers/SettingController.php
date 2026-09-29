<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'org_name' => ['nullable', 'string', 'max:255'],
            'default_interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'default_investment_return' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'swf_monthly_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        return redirect()->route('settings.index')->with('status', 'Settings saved.');
    }

    private function groupPage(string $view, string $title, string $sub)
    {
        $settings = Setting::pluck('value', 'key');

        return view($view, compact('settings', 'title', 'sub'));
    }

    private function saveGroup(Request $request, array $rules, string $route)
    {
        $data = $request->validate($rules);

        foreach ($data as $key => $value) {
            Setting::set($key, $value === null ? null : (string) $value);
        }

        return redirect()->route($route)->with('status', 'Settings saved.');
    }

    public function organization()
    {
        return $this->groupPage('settings.organization', 'Organization Settings', 'Identity, contact and branding.');
    }

    public function updateOrganization(Request $request)
    {
        return $this->saveGroup($request, [
            'org_name' => ['nullable', 'string', 'max:255'],
            'org_short_name' => ['nullable', 'string', 'max:60'],
            'org_phone' => ['nullable', 'string', 'max:30'],
            'org_email' => ['nullable', 'email'],
            'org_address' => ['nullable', 'string', 'max:255'],
            'org_registration_no' => ['nullable', 'string', 'max:100'],
        ], 'settings.organization');
    }

    public function payment()
    {
        return $this->groupPage('settings.payment', 'Payment Settings', 'Mobile-money and bank payout accounts.');
    }

    public function updatePayment(Request $request)
    {
        return $this->saveGroup($request, [
            'pay_default_method' => ['nullable', 'in:cash,mobile,bank'],
            'pay_mpesa_number' => ['nullable', 'string', 'max:30'],
            'pay_tigo_number' => ['nullable', 'string', 'max:30'],
            'pay_airtel_number' => ['nullable', 'string', 'max:30'],
            'pay_bank_name' => ['nullable', 'string', 'max:255'],
            'pay_bank_account' => ['nullable', 'string', 'max:100'],
            'pay_require_approval_above' => ['nullable', 'numeric', 'min:0'],
        ], 'settings.payment');
    }

    public function notification()
    {
        return $this->groupPage('settings.notification', 'Notification Settings', 'In-portal alerts per event.');
    }

    public function updateNotification(Request $request)
    {
        return $this->saveGroup($request, [
            'notify_loan_due' => ['nullable', 'in:0,1'],
            'notify_overdue' => ['nullable', 'in:0,1'],
            'notify_matured_investment' => ['nullable', 'in:0,1'],
            'notify_new_application' => ['nullable', 'in:0,1'],
            'notify_daily_summary' => ['nullable', 'in:0,1'],
        ], 'settings.notification');
    }

    public function communication()
    {
        return $this->groupPage('settings.communication', 'Communication Settings', 'SMS, email and WhatsApp gateways.');
    }

    public function updateCommunication(Request $request)
    {
        return $this->saveGroup($request, [
            'comm_sms_provider' => ['nullable', 'in:none,beem,nextsms,twilio'],
            'comm_sms_sender' => ['nullable', 'string', 'max:30'],
            'comm_sms_api_key' => ['nullable', 'string', 'max:255'],
            'comm_sms_token' => ['nullable', 'string', 'max:500'],
            'comm_email_from' => ['nullable', 'email'],
            'comm_whatsapp_number' => ['nullable', 'string', 'max:30'],
            'comm_whatsapp_enabled' => ['nullable', 'in:0,1'],
            'comm_member_welcome_msg' => ['nullable', 'string', 'max:500'],
            'comm_payout_sms' => ['nullable', 'string', 'max:500'],
            'comm_coupon_sms' => ['nullable', 'string', 'max:500'],
        ], 'settings.communication');
    }

    public function system()
    {
        return $this->groupPage('settings.system', 'System Settings', 'Session, audit and maintenance.');
    }
    public function updateSystem(Request $request)
    {
        return $this->saveGroup($request, [
            'sys_session_lifetime' => ['nullable', 'integer', 'min:15', 'max:1440'],
            'sys_activity_retention_days' => ['nullable', 'integer', 'min:30', 'max:3650'],
            'sys_maintenance_mode' => ['nullable', 'in:0,1'],
            'sys_currency' => ['nullable', 'string', 'max:10'],
        ], 'settings.system');
    }

    public function smsTest(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $to = preg_replace('/\D+/', '', $data['phone']);
        if (str_starts_with($to, '0')) {
            $to = '255'.substr($to, 1);
        }

        [$ok, $resp] = \App\Services\SmsService::send($to, $data['message']);

        \App\Models\SmsLog::create([
            'phone' => $to,
            'message' => $data['message'],
            'status' => $ok ? 'sent' : 'failed',
            'provider_response' => 'test: '.$resp,
            'created_by' => auth()->id(),
        ]);

        return back()->with($ok ? 'status' : 'sms_error', $ok ? 'Test SMS sent to '.$to.'.' : 'Test SMS failed: '.$resp);
    }
}
