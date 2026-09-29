<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class MemberApplication extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'user_id', 'current_step', 'name', 'first_name', 'middle_name', 'surname', 'phone', 'email', 'national_id', 'address',
        'sex', 'marital_status', 'dob', 'job', 'employer', 'statement_channel',
        'bank_name', 'bank_account',
        'member_type_id', 'member_group_id', 'status', 'notes', 'reviewed_by',
        'biography', 'referrer', 'consider_ordinary',
        'group_name', 'group_registered', 'group_leaders', 'group_bank_account', 'group_contacts',
        'savings_goal', 'goal_amount', 'goal_months', 'goal_start',
        'contributions', 'beneficiaries', 'attachments',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'goal_start' => 'date',
            'consider_ordinary' => 'boolean',
            'group_registered' => 'boolean',
            'contributions' => 'array',
            'beneficiaries' => 'array',
            'attachments' => 'array',
        ];
    }

    /** Full years of age from date of birth, null when unknown. */
    public function getAgeAttribute(): ?int
    {
        return $this->dob ? (int) $this->dob->diffInYears(now()) : null;
    }

    public function memberType(): BelongsTo
    {
        return $this->belongsTo(MemberType::class);
    }

    public function memberGroup(): BelongsTo
    {
        return $this->belongsTo(MemberGroup::class);
    }
}
