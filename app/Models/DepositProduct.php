<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class DepositProduct extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['name', 'interest_rate', 'min_amount', 'description', 'status'];

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }
}
