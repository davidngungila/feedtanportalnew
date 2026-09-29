<?php

namespace App\Models;

use App\Models\Concerns\EncryptsRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentPayout extends Model
{
    use EncryptsRouteKey;

    public const CODE_ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    protected $fillable = [
        'investment_id', 'kind', 'member_id', 'phone', 'amount', 'loan_installment',
        'swf_deduction', 'fines_deduction', 'tshirt_deduction', 'capital_cmg',
        'net_cash', 'verify_code', 'status', 'decision', 'decision_notes',
        'allocation', 'sms_sent_at', 'verified_at', 'paid_at', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime', 'paid_at' => 'datetime', 'sms_sent_at' => 'datetime', 'allocation' => 'array'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    public static function makeCode(): string
    {
        do {
            $code = '';
            $alpha = self::CODE_ALPHABET;
            for ($i = 0; $i < 6; $i++) {
                $code .= $alpha[random_int(0, strlen($alpha) - 1)];
            }
        } while (self::where('verify_code', $code)->exists());

        return $code;
    }

    public function shortUrl(): string
    {
        return url('/'.$this->verify_code);
    }

    public function intlPhone(): string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone);
        if (str_starts_with($digits, '0')) {
            return '255'.substr($digits, 1);
        }

        return $digits;
    }

    public function decisionLabel(): string
    {
        if (is_array($this->allocation) && $this->allocation) {
            return $this->allocationSummary();
        }

        return match ($this->decision) {
            'rejected' => 'Imekataliwa',
            'receive_cash' => 'Receive cash now',
            'reinvest' => 'Reinvest',
            'keep_savings' => 'Keep as savings',
            default => '—',
        };
    }

    public function allocationSummary(): string
    {
        $a = $this->allocation ?? [];
        $parts = [];
        if (($a['cash'] ?? 0) > 0) {
            $parts[] = 'Taslimu '.number_format($a['cash'], 0);
        }
        if (($a['swf'] ?? 0) > 0) {
            $parts[] = 'SWF '.number_format($a['swf'], 0);
        }
        if (($a['loan'] ?? 0) > 0) {
            $parts[] = 'Mkopo '.number_format($a['loan'], 0);
        }
        if (($a['shares'] ?? 0) > 0) {
            $parts[] = 'Hisa '.number_format($a['shares'], 0);
        }
        if (($a['reinvest'] ?? 0) > 0) {
            $parts[] = 'Wekeza '.($a['reinvest_term'] ?? '?').'y '.number_format($a['reinvest'], 0);
        }
        if (($a['savings'] ?? 0) > 0) {
            $parts[] = ucfirst($a['savings_type'] ?? 'Akiba').' '.number_format($a['savings'], 0);
        }

        if ($parts) {
            return implode(' · ', $parts);
        }

        return is_array($this->allocation) ? 'No split — TZS 0' : '—';
    }

    public const CASH_METHODS = [
        'mpesa' => 'M-Pesa',
        'tigo' => 'Tigo Pesa',
        'mixx' => 'Mixx by Yas',
        'bank' => 'Benki',
    ];

    public function allocationRows(): array
    {
        $a = $this->allocation ?? [];
        $cashLabel = 'Taslimu';
        if (! empty($a['cash_method'])) {
            $cashLabel .= ' ('.(self::CASH_METHODS[$a['cash_method']] ?? $a['cash_method']);
            if (! empty($a['cash_account'])) {
                $cashLabel .= ' · '.$a['cash_account'];
            }
            $cashLabel .= ')';
        }
        $labels = [
            'cash' => $cashLabel,
            'swf' => 'SWF',
            'loan' => 'Mkopo (rejesho)',
            'shares' => 'Hisa za duka',
            'reinvest' => 'Wekeza tena (miaka '.($a['reinvest_term'] ?? '?').')',
            'savings' => 'Akiba ('.strtoupper($a['savings_type'] ?? '').')',
        ];
        $rows = [];
        foreach ($labels as $key => $label) {
            if (($a[$key] ?? 0) > 0) {
                $rows[] = [$label, (float) $a[$key]];
            }
        }

        return $rows;
    }
}
