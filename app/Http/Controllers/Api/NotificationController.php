<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    /**
     * Récupérer le nombre de notifications non lues
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['count' => 0]);
            }
            
            // Utiliser le scope unread défini dans votre modèle
            $count = NotificationLog::where('user_id', $user->id)
                                   ->unread()
                                   ->where('channel', 'in_app') // Filtrer uniquement les notifications in-app
                                   ->count();
                    
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('NotificationCount error: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }
    /**
     * Récupérer les notifications récentes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentNotifications()
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
