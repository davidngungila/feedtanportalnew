<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class Investment extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'member_id', 'investment_product_id', 'investment_no', 'amount', 'expected_return_rate',
        'expected_return', 'start_date', 'maturity_date', 'status', 'plan', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'maturity_date' => 'date',
            'amount' => 'decimal:2',
            'expected_return' => 'decimal:2',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InvestmentProduct::class, 'investment_product_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(InvestmentReturn::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(InvestmentPayout::class);
    }
}
