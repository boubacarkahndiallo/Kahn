<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envoie un message WhatsApp à un numéro donné via l'API WhatsApp Business (Meta).
     * Retourne true si l'API a répondu avec succès, false sinon.
     */
    public function sendMessage(string $to, string $message): bool
    {
        try {
            $config = config('services.whatsapp', []);
            $apiUrl = $config['url'] ?? env('WHATSAPP_API_URL');
            $phoneId = $config['phone_number_id'] ?? env('WHATSAPP_PHONE_NUMBER_ID');
            $token = $config['token'] ?? env('WHATSAPP_TOKEN');

            if (empty($apiUrl) || empty($phoneId) || empty($token)) {
                Log::warning('WhatsApp config incomplète : vérifiez WHATSAPP_API_URL, WHATSAPP_PHONE_NUMBER_ID et WHATSAPP_TOKEN');
                return false;
            }

            // Normalise le numéro (garde seulement les chiffres)
            $toDigits = preg_replace('/[^0-9]/', '', $to);
            if (empty($toDigits)) {
                Log::warning('Numéro WhatsApp invalide : ' . $to);
                return false;
            }

            $endpoint = rtrim($apiUrl, '/') . '/' . $phoneId . '/messages';

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $toDigits,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message,
                ],
            ];

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp message envoyé à {$toDigits}", ['response' => $response->json()]);
                return true;
            }

            // Log détaillé en cas d'erreur
            Log::error('Échec envoi WhatsApp', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error("Exception lors de l'envoi WhatsApp : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère un mot de passe basé sur les 4 derniers chiffres du numéro de téléphone
     */
    public function generatePassword(string $nom, string $prenom, string $tel): string
    {
        // Extrait les 4 derniers chiffres du numéro de téléphone
        $digits = preg_replace('/[^0-9]/', '', $tel);
        $lastFourDigits = strlen($digits) >= 4 ? substr($digits, -4) : str_pad($digits, 4, '0', STR_PAD_LEFT);

        // Retourne seulement les 4 derniers chiffres
        return $lastFourDigits;
    }
}
