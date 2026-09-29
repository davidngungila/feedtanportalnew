<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class LoanProduct extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'name', 'interest_rate', 'min_amount', 'max_amount', 'duration_months', 'description', 'status',
    ];

    protected function casts(): array
    {
        return ['interest_rate' => 'decimal:2'];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
