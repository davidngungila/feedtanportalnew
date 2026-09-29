<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\EncryptsRouteKey;

class MemberGroup extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['name', 'description', 'status'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_group_member')->withTimestamps();
    }
}
