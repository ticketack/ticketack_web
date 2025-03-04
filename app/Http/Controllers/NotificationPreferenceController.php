<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\NotificationType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationPreferenceController extends Controller
{
    /**
     * Afficher la page des préférences de notifications
     */
    public function index()
    {
        $user = Auth::user();
        $notificationTypes = NotificationType::all();
        
        // Récupérer les préférences existantes
        $preferences = $user->notificationPreferences()->with('notificationType')->get();
        
        // Créer un tableau associatif des préférences par type
        $preferencesByType = [];
        foreach ($preferences as $preference) {
            $preferencesByType[$preference->notification_type_id] = $preference;
        }
        
        // Préparer les données pour la vue
        $preferencesData = [];
        foreach ($notificationTypes as $type) {
            $preference = $preferencesByType[$type->id] ?? null;
            
            $preferencesData[] = [
                'id' => $preference ? $preference->id : null,
                'notification_type_id' => $type->id,
                'notification_type_key' => $type->key,
                'notification_type_name' => $type->name,
                'notification_type_description' => $type->description,
                'in_app' => $preference ? $preference->in_app : true,
                'email' => $preference ? $preference->email : false,
                'sms' => $preference ? $preference->sms : false,
            ];
        }
        
        return Inertia::render('Profile/NotificationPreferences', [
            'preferences' => $preferencesData,
            'user' => [
                'phone_number' => $user->phone_number,
            ],
        ]);
    }
    
    /**
     * Mettre à jour les préférences de notifications
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.notification_type_id' => 'required|exists:notification_types,id',
            'preferences.*.in_app' => 'required|boolean',
            'preferences.*.email' => 'required|boolean',
            'preferences.*.sms' => 'required|boolean',
            'phone_number' => 'nullable|string|max:20',
        ]);
        
        // Mettre à jour les préférences
        foreach ($data['preferences'] as $preferenceData) {
            $user->notificationPreferences()->updateOrCreate(
                ['notification_type_id' => $preferenceData['notification_type_id']],
                [
                    'in_app' => $preferenceData['in_app'],
                    'email' => $preferenceData['email'],
                    'sms' => $preferenceData['sms'],
                ]
            );
        }
        
        // Mettre à jour les informations de l'utilisateur
        $user->update([
            'phone_number' => $data['phone_number'] ?? null,
        ]);
        
        return redirect()->back()->with('success', 'Préférences de notifications mises à jour avec succès.');
    }
}
