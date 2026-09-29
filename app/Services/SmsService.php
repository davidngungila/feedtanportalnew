<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class SmsService
{
    public const SINGLE_URL = 'https://messaging-service.co.tz/api/sms/v2/text/single';
    public const MULTI_URL = 'https://messaging-service.co.tz/api/sms/v2/text/multi';

    public static function credentials(): array
    {
        return [
            Setting::get('comm_sms_provider', 'none'),
            Setting::get('comm_sms_sender', 'FEEDTAN'),
            Setting::get('comm_sms_token', '') ?: Setting::get('comm_sms_api_key', ''),
        ];
    }

    public static function defaultTemplate(): string
    {
        return Setting::get('comm_payout_sms')
            ?: 'Habari {name}, hongera! Umepata gawio la TZS {amount} kwa ajili ya uwekezaji wako wa FIA. Thibitisha malipo yako hapa: {link}. Endelea kuwekeza na kupata gawio zaidi.';
    }

    public static function defaultCouponTemplate(): string
    {
        return Setting::get('comm_coupon_sms')
            ?: 'Habari {name}, Hongera! Umepata gawio la TZS {amount} kutoka kwenye uwekezaji wako wa FIA. Thibitisha malipo yako hapa: {link}. Endelea kuwekeza na kupata gawio zaidi.';
    }

    public static function buildCouponMessage(\App\Models\InvestmentPayout $payout, ?string $template = null): string
    {
        $template ??= self::defaultCouponTemplate();

        return strtr($template, [
            '{name}' => $payout->member->name ?? 'mwanachama',
            '{amount}' => number_format((float) $payout->net_cash, 0),
            '{link}' => $payout->shortUrl(),
            '{code}' => $payout->verify_code,
            '{phone}' => $payout->phone,
        ]);
    }

    public static function buildMessage(\App\Models\InvestmentPayout $payout, ?string $template = null): string
    {
        $template ??= self::defaultTemplate();

        return strtr($template, [
            '{name}' => $payout->member->name ?? 'mwanachama',
            '{amount}' => number_format((float) $payout->net_cash, 0),
            '{link}' => $payout->shortUrl(),
            '{code}' => $payout->verify_code,
            '{phone}' => $payout->phone,
        ]);
    }

    protected static function checkReady(): ?string
    {
        [$provider, $sender, $token] = self::credentials();
        if ($provider === 'none' || $provider === '') {
            return 'No SMS provider selected — choose one in Communication Settings.';
        }
        if ($token === '') {
            return 'No API Token saved — paste the provider token in Communication Settings.';
        }
        if ($sender === '') {
            return 'No Sender ID saved — set it in Communication Settings.';
        }

        return null;
    }

    public static function send(string $to, string $message): array
    {
        if ($problem = self::checkReady()) {
            return [false, $problem];
        }

        [$provider, $sender, $token] = self::credentials();

        try {
            if ($provider === 'nextsms') {
                $res = Http::timeout(20)->withToken($token)->post(self::SINGLE_URL, [
                    'from' => $sender,
                    'to' => $to,
                    'text' => $message,
                ]);
            } elseif ($provider === 'beem') {
                $res = Http::timeout(20)->withBasicAuth($token, '')->post('https://apisms.beem.africa/v1/send', [
                    'source_addr' => $sender,
                    'encoding' => 0,
                    'schedule_time' => '',
                    'message' => $message,
                    'recipients' => [['recipient_id' => 1, 'dest_addr' => $to]],
                ]);
            } else { // twilio-style
                $res = Http::timeout(20)->asForm()->withBasicAuth($token, '')->post('https://api.twilio.com/messages', [
                    'From' => $sender,
                    'To' => $to,
                    'Body' => $message,
                ]);
            }

            $ok = $res->successful();
            $body = substr((string) $res->body(), 0, 1000);

            return [$ok, $ok ? 'Sent: '.$body : 'Provider error: '.$body];
        } catch (\Throwable $e) {
            return [false, 'Send failed: '.$e->getMessage()];
        }
    }

    public static function sendBulk(array $items): array
    {
        // $items: [['to' => ..., 'text' => ...], ...] — one NextSMS v2 multi request.
        if ($problem = self::checkReady()) {
            return [false, $problem];
        }

        [$provider, $sender, $token] = self::credentials();
        if ($provider !== 'nextsms') {
            return [false, 'Bulk endpoint is NextSMS v2 only — singles will be used instead.'];
        }

        try {
            $res = Http::timeout(30)->withToken($token)->post(self::MULTI_URL, [
                'messages' => array_map(fn ($m) => ['from' => $sender, 'to' => $m['to'], 'text' => $m['text']], array_values($items)),
                'flash' => 0,
                'reference' => 'BULK-'.now()->format('YmdHis'),
            ]);

            $ok = $res->successful();

            return [$ok, substr((string) $res->body(), 0, 1000)];
        } catch (\Throwable $e) {
            return [false, 'Bulk send failed: '.$e->getMessage()];
        }
    }
}
