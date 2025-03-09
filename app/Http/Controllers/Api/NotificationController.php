<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Récupérer le nombre de notifications non lues
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $user = Auth::user();
        $count = NotificationLog::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Récupérer les notifications récentes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recent()
    {
        $user = Auth::user();
        $notifications = NotificationLog::where('user_id', $user->id)
            ->with('notificationType')
            ->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'content' => $notification->content,
                    'channel' => $notification->channel,
                    'is_read' => $notification->is_read,
                    'sent_at' => $notification->sent_at,
                    'notification_type_name' => $notification->notificationType->name,
                    'notification_type_key' => $notification->notificationType->key,
                ];
            });

        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
