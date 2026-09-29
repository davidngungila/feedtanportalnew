<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\EncryptsRouteKey;

#[Fillable(['name', 'email', 'phone', 'avatar_path', 'password', 'role', 'member_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use EncryptsRouteKey;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function avatarUrl(): ?string
    {
        if ($this->avatar_path) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar_path);
        }

        // Fall back to the passport photo from the member application,
        // so it shows in the header, account page and portal profile.
        try {
            $app = MemberApplication::where(function ($q) {
                $q->where('user_id', $this->id);
                if ($this->email) {
                    $q->orWhere(fn ($w) => $w->whereNull('user_id')->where('email', $this->email));
                }
            })->latest('id')->first();
            $passport = $app?->attachments['passport'] ?? null;
            if ($passport && \Illuminate\Support\Facades\Storage::disk('public')->exists($passport)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($passport);
            }
        } catch (\Throwable) {
        }

        return null;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function roleSlugs(): array
    {
        if ($this->relationLoaded('roles') || $this->exists) {
            $slugs = $this->roles()->pluck('slug')->all();
            if ($slugs) {
                return $slugs;
            }
        }

        return $this->role ? [$this->role] : [];
    }

    public function hasRole(string ...$slugs): bool
    {
        // Administrators bypass everything.
        $mine = $this->roleSlugs();
        if (in_array('administrator', $mine, true) || in_array('admin', $mine, true)) {
            return true;
        }

        foreach ($slugs as $s) {
            if (in_array($s, $mine, true)) {
                return true;
            }
            // Legacy aliases.
            if ($s === 'administrator' && in_array('admin', $mine, true)) {
                return true;
            }
        }

        return false;
    }

    public function roleLabel(): string
    {
        $slugs = $this->roleSlugs();
        if (! $slugs) {
            return 'Staff';
        }

        return collect($slugs)->map(fn ($s) => ucwords(str_replace('_', ' ', $s)))->join(', ');
    }

    public const ROLE_PRIORITY = [
        'administrator',
        'chairperson',
        'secretary',
        'accountant',
        'swf_officer',
        'deposit_officer',
        'investment_officer',
        'loan_officer',
        'member',
        'applicant',
    ];

    public function primaryRole(): ?string
    {
        $slugs = array_map(
            fn ($s) => $s === 'admin' ? 'administrator' : $s,
            $this->roleSlugs()
        );

        if (! $slugs) {
            return null;
        }

        foreach (self::ROLE_PRIORITY as $priority) {
            if (in_array($priority, $slugs, true)) {
                return $priority;
            }
        }

        return $slugs[0];
    }

    public function primaryRoleLabel(): string
    {
        $role = $this->primaryRole();
        if (! $role) {
            return 'Staff';
        }

        return function_exists('role_names') && isset(role_names()[$role])
            ? role_names()[$role]
            : ucwords(str_replace('_', ' ', $role));
    }
}
