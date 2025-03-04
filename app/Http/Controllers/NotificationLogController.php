<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationLogController extends Controller
{
    /**
     * Afficher le journal des notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notificationLogs()
            ->with('notificationType')
            ->orderBy('created_at', 'desc');
        
        // Filtrer par canal
        if ($request->has('channel') && $request->channel) {
            $query->where('channel', $request->channel);
        }
        
        // Filtrer par type de notification
        if ($request->has('notification_type_id') && $request->notification_type_id) {
            $query->where('notification_type_id', $request->notification_type_id);
        }
        
        // Filtrer par statut de lecture
        if ($request->has('is_read') && $request->is_read !== null) {
            $query->where('is_read', $request->is_read);
        }
        
        $logs = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Profile/NotificationLogs', [
            'logs' => $logs,
            'filters' => $request->only(['channel', 'notification_type_id', 'is_read']),
        ]);
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(NotificationLog $notificationLog)
    {
        // Vérifier que la notification appartient à l'utilisateur actuel
        if ($notificationLog->user_id !== Auth::id()) {
            abort(403);
        }
        
        $notificationLog->update(['is_read' => true]);
        
        return redirect()->back();
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notificationLogs()->where('is_read', false);
        
        // Filtrer par canal si spécifié
        if ($request->has('channel') && $request->channel) {
            $query->where('channel', $request->channel);
        }
        
        $query->update(['is_read' => true]);
        
        return redirect()->back();
    }
}
