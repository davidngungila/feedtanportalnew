<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class FinanceTransaction extends Model
{
    use EncryptsRouteKey;
    public const CATEGORIES = ['operating', 'fee', 'charge', 'interest', 'commission', 'adjustment', 'other'];

    protected $fillable = [
        'reference', 'type', 'category', 'finance_account_id', 'member_id',
        'amount', 'transacted_at', 'description', 'journal_entry_id', 'created_by',
    ];

    protected function casts(): array
    {
        return ['transacted_at' => 'date', 'amount' => 'decimal:2'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'finance_account_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function categoryLabel(): string
    {
        return ucwords(str_replace('_', ' ', $this->category));
    }
}
