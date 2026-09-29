<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class FinanceReconciliation extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'finance_account_id', 'statement_date', 'statement_balance',
        'system_balance', 'variance', 'status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['statement_date' => 'date'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'finance_account_id');
    }
}
