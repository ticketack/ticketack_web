<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'description',
    ];

    /**
     * Get the preferences for this notification type.
     */
    public function preferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    /**
     * Get the logs for this notification type.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }
}
