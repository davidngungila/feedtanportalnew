<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class MemberApplication extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'user_id', 'current_step', 'name', 'phone', 'email', 'national_id', 'address',
        'member_type_id', 'member_group_id', 'status', 'notes', 'reviewed_by',
    ];

    public function memberType(): BelongsTo
    {
        return $this->belongsTo(MemberType::class);
    }

    public function memberGroup(): BelongsTo
    {
        return $this->belongsTo(MemberGroup::class);
    }
}
