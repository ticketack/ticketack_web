<?php

namespace App\Events;

use App\Models\NotificationLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(NotificationLog $notification)
    {
        $this->notification = [
            'id' => $notification->id,
            'user_id' => $notification->user_id,
            'content' => $notification->content,
            'channel' => $notification->channel,
            'is_read' => $notification->is_read,
            'sent_at' => $notification->sent_at,
            'notification_type_name' => $notification->notificationType->name,
            'notification_type_key' => $notification->notificationType->key,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->notification['user_id']),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new.notification';
    }
}
