<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class FinanceAccount extends Model
{
    use EncryptsRouteKey;
    public const TYPES = ['asset', 'liability', 'equity', 'income', 'expense'];

    protected $fillable = ['code', 'name', 'type', 'parent_id', 'opening_balance', 'description', 'status'];

    protected function casts(): array
    {
        return ['opening_balance' => 'decimal:2'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function postedDebit(?string $from = null, ?string $to = null): float
    {
        return $this->postedSum('debit', $from, $to);
    }

    public function postedCredit(?string $from = null, ?string $to = null): float
    {
        return $this->postedSum('credit', $from, $to);
    }

    protected function postedSum(string $column, ?string $from, ?string $to): float
    {
        $q = $this->lines()->whereHas('entry', fn ($e) => $e->where('status', 'posted'));
        if ($from) {
            $q->whereHas('entry', fn ($e) => $e->whereDate('entry_date', '>=', $from));
        }
        if ($to) {
            $q->whereHas('entry', fn ($e) => $e->whereDate('entry_date', '<=', $to));
        }

        return (float) $q->sum($column);
    }

    public function balance(?string $from = null, ?string $to = null): float
    {
        $dr = $this->postedDebit($from, $to);
        $cr = $this->postedCredit($from, $to);

        return match ($this->type) {
            'asset', 'expense' => (float) $this->opening_balance + $dr - $cr,
            default => (float) $this->opening_balance + $cr - $dr,
        };
    }

    public function typeLabel(): string
    {
        return ucfirst($this->type);
    }
}
