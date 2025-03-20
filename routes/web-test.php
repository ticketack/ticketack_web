<?php

use App\Events\NewNotification;
use App\Models\NotificationLog;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route pour la page de test Pusher
Route::get('/pusher-test', function () {
    return Inertia::render('PusherTest');
})->middleware('auth')->name('pusher.test');

// Route de test pour Pusher (API)
Route::get('/test-pusher', function () {
    // Vérifier si l'utilisateur est connecté
    if (!auth()->check()) {
        return response()->json(['error' => 'Utilisateur non connecté'], 401);
    }
    
    $user = auth()->user();
    
    // Trouver ou créer un type de notification
    $notificationType = NotificationType::firstOrCreate(
        ['key' => 'test_notification'],
        [
            'name' => 'Test Notification',
            'description' => 'Notification de test pour Pusher',
        ]
    );
    
    // Créer une notification de test
    $notification = new NotificationLog([
        'user_id' => $user->id,
        'notification_type_id' => $notificationType->id,
        'content' => 'Ceci est une notification de test envoyée à ' . now()->format('H:i:s'),
        'channel' => 'web',
        'is_read' => false,
        'sent_at' => now(),
    ]);
    
    $notification->save();
    
    // Diffuser l'événement
    event(new NewNotification($notification));
    
    return response()->json([
        'success' => true,
        'message' => 'Notification de test envoyée',
        'notification' => $notification,
    ]);
})->middleware('auth')->name('test.pusher');
