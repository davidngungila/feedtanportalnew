<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class FinanceTransfer extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'reference', 'from_account_id', 'to_account_id', 'amount',
        'transferred_at', 'notes', 'journal_entry_id', 'created_by',
    ];

    protected function casts(): array
    {
        return ['transferred_at' => 'date', 'amount' => 'decimal:2'];
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'to_account_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
