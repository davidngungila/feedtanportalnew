<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class LoanRepayment extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'loan_id', 'receipt_no', 'amount', 'paid_at', 'method', 'notes', 'received_by',
    ];

    protected function casts(): array
    {
        return ['paid_at' => 'date', 'amount' => 'decimal:2'];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
