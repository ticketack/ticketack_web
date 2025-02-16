<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('permissions.name', $permission)
                    ->where('permission_role.granted', true);
            })
            ->exists();
    }

    public function can($abilities, $arguments = [])
    {
        if (is_string($abilities)) {
            // Débogage
            \Log::info('Checking permission: ' . $abilities);
            \Log::info('User roles: ' . $this->roles()->pluck('name')->implode(', '));
            $hasPermission = $this->hasPermissionTo($abilities);
            \Log::info('Has permission: ' . ($hasPermission ? 'true' : 'false'));
            return $hasPermission;
        }
        return false;
    }
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
