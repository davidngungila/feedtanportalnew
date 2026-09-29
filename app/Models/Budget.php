<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class Budget extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['finance_account_id', 'financial_period_id', 'budgeted', 'notes'];

    protected function casts(): array
    {
        return ['budgeted' => 'decimal:2'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'finance_account_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(FinancialPeriod::class, 'financial_period_id');
    }

    public function actual(): float
    {
        $from = $this->period?->starts_at?->toDateString();
        $to = $this->period?->ends_at?->toDateString();

        return $this->account ? $this->account->balance($from, $to) - (float) $this->account->opening_balance : 0;
    }
}
