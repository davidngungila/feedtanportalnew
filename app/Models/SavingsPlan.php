<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class SavingsPlan extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['name', 'target_amount', 'duration_months', 'deposit_product_id', 'description', 'status'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(DepositProduct::class, 'deposit_product_id');
    }
}
