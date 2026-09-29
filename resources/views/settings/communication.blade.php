@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="view-head"><div><h2>{{ $title }}</h2><p class="sub">{{ $sub }} Each section opens in its own window.</p></div><div class="view-actions"><a href="{{ route('settings.index') }}" class="btn btn-ghost">General</a></div></div>

    @if(session('sms_error'))<div style="background:var(--danger-100);color:var(--danger);border-radius:10px;padding:12px 16px;font-size:13.5px;font-weight:600;margin-bottom:20px;">{{ session('sms_error') }}</div>@endif

    <div class="card-grid">
        <div class="mini-card">
            <div class="mc-top"><span class="mc-name">SMS Gateway</span><span class="tag {{ ($settings['comm_sms_provider'] ?? 'none') === 'none' ? 'tag-grey' : 'tag-green' }}">{{ ucfirst($settings['comm_sms_provider'] ?? 'none') }}</span></div>
            <div class="mc-label">Sender: {{ $settings['comm_sms_sender'] ?? '—' }} · Token: {{ ! empty($settings['comm_sms_token'] ?? $settings['comm_sms_api_key'] ?? '') ? 'saved ✓' : 'missing' }}</div>
            <div class="view-actions" style="margin-top:12px;"><button type="button" class="btn btn-primary btn-sm" onclick="openModal('smsGatewayModal')">Configure</button></div>
        </div>
        <div class="mini-card">
            <div class="mc-top"><span class="mc-name">Payout SMS Template</span></div>
            <div class="mc-label">{{ \Illuminate\Support\Str::limit($settings['comm_payout_sms'] ?? 'Default Swahili template', 90) }}</div>
            <div class="view-actions" style="margin-top:12px;"><button type="button" class="btn btn-primary btn-sm" onclick="openModal('smsTemplateModal')">Edit template</button></div>
        </div>
        <div class="mini-card">
            <div class="mc-top"><span class="mc-name">Email &amp; WhatsApp</span><span class="tag {{ ($settings['comm_whatsapp_enabled'] ?? '0') === '1' ? 'tag-green' : 'tag-grey' }}">WA {{ ($settings['comm_whatsapp_enabled'] ?? '0') === '1' ? 'on' : 'off' }}</span></div>
            <div class="mc-label">{{ $settings['comm_email_from'] ?? 'No sender email' }} · {{ $settings['comm_whatsapp_number'] ?? 'No WA number' }}</div>
            <div class="view-actions" style="margin-top:12px;"><button type="button" class="btn btn-primary btn-sm" onclick="openModal('emailWaModal')">Configure</button></div>
        </div>
        <div class="mini-card">
            <div class="mc-top"><span class="mc-name">Send Test SMS</span></div>
            <div class="mc-label">Verify your API Token + Sender ID with a live message.</div>
            <div class="view-actions" style="margin-top:12px;"><button type="button" class="btn btn-ghost btn-sm" onclick="openModal('smsTestModal')">Open tester</button></div>
        </div>
    </div>

    <div class="modal-backdrop" id="smsGatewayModal">
        <div class="popup" style="padding:22px;">
            <div style="text-align:center;margin-bottom:14px;"><h3 style="font-size:16px;margin:0;">SMS Gateway</h3><div class="cell-sub">NextSMS v2 uses Bearer API Token + Sender ID</div></div>
            <form method="POST" action="{{ route('settings.communication.update') }}">@csrf @method('PUT')
                <div class="field"><label>Provider *</label><select name="comm_sms_provider"><option value="none">None</option>@foreach(['beem','nextsms','twilio'] as $p)<option value="{{ $p }}" {{ ($settings['comm_sms_provider'] ?? 'none') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>@endforeach</select></div>
                <div class="field"><label>Sender ID *</label><input name="comm_sms_sender" value="{{ $settings['comm_sms_sender'] ?? '' }}" placeholder="e.g. FEEDTAN"></div>
                <div class="field"><label>API Token *</label><input type="password" name="comm_sms_token" value="{{ $settings['comm_sms_token'] ?? '' }}" placeholder="Paste Bearer token from provider"></div>
                <div style="display:flex;gap:10px;"><button type="button" class="btn btn-ghost" style="flex:1;" onclick="closeModal('smsGatewayModal')">Cancel</button><button type="submit" class="btn btn-primary" style="flex:1;">Save</button></div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="smsTemplateModal">
        <div class="popup" style="padding:22px;">
            <div style="text-align:center;margin-bottom:14px;"><h3 style="font-size:16px;margin:0;">Payout SMS Template</h3><div class="cell-sub">{name} {amount} {link} {code} {phone}</div></div>
            <form method="POST" action="{{ route('settings.communication.update') }}">@csrf @method('PUT')
                <div class="field"><label>Verification message</label><textarea name="comm_payout_sms" rows="4">{{ $settings['comm_payout_sms'] ?? '' }}</textarea></div>
                <div class="field"><label>Member welcome message</label><textarea name="comm_member_welcome_msg" rows="3" placeholder="Hi {name}, welcome!">{{ $settings['comm_member_welcome_msg'] ?? '' }}</textarea></div>
                <div style="display:flex;gap:10px;"><button type="button" class="btn btn-ghost" style="flex:1;" onclick="closeModal('smsTemplateModal')">Cancel</button><button type="submit" class="btn btn-primary" style="flex:1;">Save</button></div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="emailWaModal">
        <div class="popup" style="padding:22px;">
            <div style="text-align:center;margin-bottom:14px;"><h3 style="font-size:16px;margin:0;">Email &amp; WhatsApp</h3></div>
            <form method="POST" action="{{ route('settings.communication.update') }}">@csrf @method('PUT')
                <div class="field"><label>Email from address</label><input type="email" name="comm_email_from" value="{{ $settings['comm_email_from'] ?? '' }}"></div>
                <div class="field"><label>WhatsApp number</label><input name="comm_whatsapp_number" value="{{ $settings['comm_whatsapp_number'] ?? '' }}"></div>
                <div class="field"><label>WhatsApp enabled</label><select name="comm_whatsapp_enabled"><option value="1" {{ ($settings['comm_whatsapp_enabled'] ?? '0') === '1' ? 'selected' : '' }}>On</option><option value="0" {{ ($settings['comm_whatsapp_enabled'] ?? '0') === '0' ? 'selected' : '' }}>Off</option></select></div>
                <div style="display:flex;gap:10px;"><button type="button" class="btn btn-ghost" style="flex:1;" onclick="closeModal('emailWaModal')">Cancel</button><button type="submit" class="btn btn-primary" style="flex:1;">Save</button></div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="smsTestModal">
        <div class="popup" style="padding:22px;">
            <div style="text-align:center;margin-bottom:14px;"><h3 style="font-size:16px;margin:0;">Send Test SMS</h3><div class="cell-sub">Result is logged like all SMS</div></div>
            <form method="POST" action="{{ route('settings.sms.test') }}">@csrf
                <div class="field"><label>Phone *</label><input name="phone" required placeholder="0712345678 or 255712345678"></div>
                <div class="field"><label>Message *</label><textarea name="message" rows="3" required placeholder="Hello from FeedTan CMG — test message">Hello from FeedTan CMG — test message</textarea></div>
                <div style="display:flex;gap:10px;"><button type="button" class="btn btn-ghost" style="flex:1;" onclick="closeModal('smsTestModal')">Cancel</button><button type="submit" class="btn btn-primary" style="flex:1;">Send test</button></div>
            </form>
        </div>
    </div>
@endsection
