<?php

namespace App\Models;

use App\Events\NewNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type_id',
        'channel',
        'content',
        'is_read',
        'metadata',
        'sent_at',
        'status',
        'error',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notification type that owns the notification log.
     */
    public function notificationType(): BelongsTo
    {
        return $this->belongsTo(NotificationType::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include notifications of a specific channel.
     */
    public function scopeChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }
    
    /**
     * The "created" event handler.
     */
    protected static function booted()
    {
        static::created(function ($notification) {
            // Ne diffuser que les notifications in-app
            if ($notification->channel === 'in_app') {
                broadcast(new NewNotification($notification));
            }
        });
    }
}
