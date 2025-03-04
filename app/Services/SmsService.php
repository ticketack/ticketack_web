<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $baseUrl = 'https://api.mailingvox.com/v1';

    /**
     * Obtenir la clé API SMS depuis les paramètres
     * 
     * @return string|null
     */
    protected function getApiKey()
    {
        return Setting::getValue('sms_api_key', '');
    }

    /**
     * Envoyer un SMS
     *
     * @param string $to Numéro de téléphone du destinataire
     * @param string $message Contenu du message
     * @param string|null $customApiKey Clé API personnalisée (optionnel)
     * @return array Résultat de l'envoi
     */
    public function send($to, $message, $customApiKey = null)
    {
        $apiKey = $customApiKey ?? $this->getApiKey();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/sms/send', [
                'to' => $this->formatPhoneNumber($to),
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('SMS sending failed', [
                'error' => $response->body(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Échec de l\'envoi du SMS',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('SMS service exception', [
                'exception' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Formater le numéro de téléphone
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Supprimer tous les caractères non numériques
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Ajouter le préfixe international si nécessaire
        if (substr($phoneNumber, 0, 2) === '06' || substr($phoneNumber, 0, 2) === '07') {
            $phoneNumber = '33' . substr($phoneNumber, 1);
        }

        // S'assurer que le numéro commence par '+'
        if (substr($phoneNumber, 0, 1) !== '+') {
            $phoneNumber = '+' . $phoneNumber;
        }

        return $phoneNumber;
    }
}
