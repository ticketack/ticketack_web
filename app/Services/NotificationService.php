<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\NotificationType;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Envoyer une notification à un utilisateur
     *
     * @param User $user Utilisateur destinataire
     * @param string $notificationTypeKey Clé du type de notification
     * @param string $content Contenu de la notification
     * @param array $metadata Métadonnées supplémentaires
     * @return array Résultat de l'envoi
     */
    public function sendNotification(User $user, string $notificationTypeKey, string $content, array $metadata = [])
    {
        $notificationType = NotificationType::where('key', $notificationTypeKey)->first();

        if (!$notificationType) {
            Log::error('Type de notification non trouvé', ['key' => $notificationTypeKey]);
            return [
                'success' => false,
                'error' => 'Type de notification non trouvé: ' . $notificationTypeKey,
            ];
        }

        $preference = $user->notificationPreferences()
            ->where('notification_type_id', $notificationType->id)
            ->first();

        // Si aucune préférence n'existe, on crée une préférence par défaut (in_app uniquement)
        if (!$preference) {
            $preference = $user->notificationPreferences()->create([
                'notification_type_id' => $notificationType->id,
                'in_app' => true,
                'email' => false,
                'sms' => false,
            ]);
        }

        $results = [];

        // Notification in-app
        if ($preference->in_app) {
            $results['in_app'] = $this->createNotificationLog($user, $notificationType, 'in_app', $content, $metadata);
        }

        // Notification par email
        if ($preference->email && $user->email) {
            $results['email'] = $this->sendEmail($user, $notificationType, $content, $metadata);
        }

        // Notification par SMS
        if ($preference->sms && $user->phone_number) {
            $results['sms'] = $this->sendSms($user, $notificationType, $content, $metadata);
        }

        return [
            'success' => true,
            'results' => $results,
        ];
    }

    /**
     * Créer un log de notification
     *
     * @param User $user Utilisateur destinataire
     * @param NotificationType $notificationType Type de notification
     * @param string $channel Canal de notification
     * @param string $content Contenu de la notification
     * @param array $metadata Métadonnées supplémentaires
     * @param string $status Statut de la notification
     * @param string|null $error Message d'erreur
     * @return NotificationLog
     */
    protected function createNotificationLog(
        User $user,
        NotificationType $notificationType,
        string $channel,
        string $content,
        array $metadata = [],
        string $status = 'sent',
        string $error = null
    ) {
        return NotificationLog::create([
            'user_id' => $user->id,
            'notification_type_id' => $notificationType->id,
            'channel' => $channel,
            'content' => $content,
            'metadata' => $metadata,
            'sent_at' => now(),
            'status' => $status,
            'error' => $error,
        ]);
    }

    /**
     * Envoyer un email
     *
     * @param User $user Utilisateur destinataire
     * @param NotificationType $notificationType Type de notification
     * @param string $content Contenu de la notification
     * @param array $metadata Métadonnées supplémentaires
     * @return NotificationLog
     */
    protected function sendEmail(User $user, NotificationType $notificationType, string $content, array $metadata = [])
    {
        try {
            // Ici, vous pouvez utiliser Mail::to() pour envoyer un email
            // Pour l'instant, nous simulons l'envoi d'email
            
            // Mail::to($user->email)->send(new \App\Mail\NotificationMail($content, $notificationType->name, $metadata));
            
            // Log de la notification
            return $this->createNotificationLog(
                $user,
                $notificationType,
                'email',
                $content,
                $metadata,
                'sent'
            );
        } catch (\Exception $e) {
            Log::error('Échec de l\'envoi d\'email', [
                'exception' => $e->getMessage(),
                'user' => $user->id,
                'notification_type' => $notificationType->key,
            ]);

            return $this->createNotificationLog(
                $user,
                $notificationType,
                'email',
                $content,
                $metadata,
                'failed',
                $e->getMessage()
            );
        }
    }

    /**
     * Envoyer un SMS
     *
     * @param User $user Utilisateur destinataire
     * @param NotificationType $notificationType Type de notification
     * @param string $content Contenu de la notification
     * @param array $metadata Métadonnées supplémentaires
     * @return NotificationLog
     */
    protected function sendSms(User $user, NotificationType $notificationType, string $content, array $metadata = [])
    {
        try {
            // Utiliser la clé API globale configurée dans les paramètres
            $result = $this->smsService->send($user->phone_number, $content);
            
            if ($result['success']) {
                return $this->createNotificationLog(
                    $user,
                    $notificationType,
                    'sms',
                    $content,
                    array_merge($metadata, ['sms_response' => $result['data']]),
                    'sent'
                );
            } else {
                return $this->createNotificationLog(
                    $user,
                    $notificationType,
                    'sms',
                    $content,
                    array_merge($metadata, ['sms_error' => $result['error']]),
                    'failed',
                    $result['error']
                );
            }
        } catch (\Exception $e) {
            Log::error('Échec de l\'envoi de SMS', [
                'exception' => $e->getMessage(),
                'user' => $user->id,
                'notification_type' => $notificationType->key,
            ]);

            return $this->createNotificationLog(
                $user,
                $notificationType,
                'sms',
                $content,
                $metadata,
                'failed',
                $e->getMessage()
            );
        }
    }
}
