<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'description'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withPivot('granted')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->permissions()
            ->where('name', $permission)
            ->wherePivot('granted', true)
            ->exists();
    }
}
