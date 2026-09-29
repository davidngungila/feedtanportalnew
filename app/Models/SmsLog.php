<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'investment_payout_id', 'member_id', 'phone', 'message',
        'status', 'provider_response', 'created_by',
    ];

    public function payout(): BelongsTo
    {
        return $this->belongsTo(InvestmentPayout::class, 'investment_payout_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
