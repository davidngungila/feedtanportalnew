<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class Member extends Model
{
    use EncryptsRouteKey;
    protected $fillable = [
        'member_no', 'member_type_id', 'name', 'phone', 'email', 'national_id',
        'address', 'join_date', 'status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['join_date' => 'date'];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function swfEntries(): HasMany
    {
        return $this->hasMany(SwfEntry::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(InvestmentPayout::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function memberType(): BelongsTo
    {
        return $this->belongsTo(MemberType::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(MemberGroup::class, 'member_group_member')->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MemberDocument::class);
    }

    public function savingsBalance(): float
    {
        $in = (float) $this->deposits()->where('type', 'deposit')->sum('amount');
        $out = (float) $this->deposits()->where('type', 'withdrawal')->sum('amount');

        return $in - $out;
    }

    public function loanOutstanding(): float
    {
        $disbursed = (float) $this->loans()->whereIn('status', ['active', 'overdue', 'paid'])->sum('total_payable');
        $repaid = (float) LoanRepayment::whereIn('loan_id', $this->loans()->pluck('id'))->sum('amount');

        return max(0, $disbursed - $repaid);
    }

    public function swfBalance(): float
    {
        $in = (float) $this->swfEntries()->where('type', 'contribution')->sum('amount');
        $out = (float) $this->swfEntries()->whereIn('type', ['payout', 'claim', 'deduction'])->sum('amount');

        return $in - $out;
    }
}
