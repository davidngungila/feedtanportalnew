<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\EncryptsRouteKey;

class MemberType extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['name', 'description', 'status'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
