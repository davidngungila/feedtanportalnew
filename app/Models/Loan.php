<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class Loan extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'member_id', 'loan_product_id', 'loan_no', 'principal', 'interest_rate', 'interest_amount',
        'total_payable', 'disbursed_at', 'due_date', 'status', 'purpose', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'disbursed_at' => 'date',
            'due_date' => 'date',
            'principal' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'total_payable' => 'decimal:2',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function totalRepaid(): float
    {
        return (float) $this->repayments()->sum('amount');
    }

    public function outstanding(): float
    {
        return max(0, (float) $this->total_payable - $this->totalRepaid());
    }
}
