<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class FinancialPeriod extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['name', 'starts_at', 'ends_at', 'status'];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'ends_at' => 'date'];
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function contains(string $date): bool
    {
        return $date >= $this->starts_at->toDateString() && $date <= $this->ends_at->toDateString();
    }
}
