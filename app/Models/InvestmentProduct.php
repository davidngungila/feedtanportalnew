<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class InvestmentProduct extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['name', 'return_rate', 'min_amount', 'duration_months', 'description', 'status'];

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class, 'investment_product_id');
    }
}
