<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class LoanApplication extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'member_id', 'loan_product_id', 'amount', 'purpose', 'status', 'notes', 'reviewed_by',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }
}
