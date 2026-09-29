<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\EncryptsRouteKey;

class Role extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['slug', 'name', 'description'];

    public const SLUGS = [
        'administrator',
        'chairperson',
        'secretary',
        'accountant',
        'swf_officer',
        'deposit_officer',
        'investment_officer',
        'loan_officer',
        'member',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }
}
