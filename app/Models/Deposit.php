<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class Deposit extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'member_id', 'deposit_product_id', 'receipt_no', 'type', 'amount', 'method', 'transacted_at', 'notes', 'received_by',
    ];

    protected function casts(): array
    {
        return ['transacted_at' => 'date', 'amount' => 'decimal:2'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(DepositProduct::class, 'deposit_product_id');
    }
}
