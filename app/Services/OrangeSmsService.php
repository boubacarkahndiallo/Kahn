<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Location; // Modèle représentant la location dans ta DB

class SmsService
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $senderNumber;

    public function __construct()
    {
        $this->client = new Client();
        $this->clientId = config('services.orange.client_id');
        $this->clientSecret = config('services.orange.client_secret');
        $this->senderNumber = config('services.orange.sender'); // Numéro d'expéditeur Orange
    }

    // Obtenir le token OAuth
    protected function getAccessToken()
    {
        $response = $this->client->post('https://api.orange.com/oauth/v3/token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'] ?? null;
    }

    // Envoyer un SMS à un client après location
    public function sendLocationSms($locationId)
    {
        // Récupérer la location dans la base de données
        $location = Location::find($locationId);
        if (!$location) {
            return "❌ Location introuvable.";
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return "❌ Impossible d'obtenir le token d'accès.";
        }

        // Construire le message
        $message = "Bonjour {$location->client_name},\nVotre location est confirmée !\nMaison : {$location->coordonnees_maison}\nMontant : {$location->montant}.\nMerci pour votre confiance !";

        $url = "https://api.orange.com/smsmessaging/v1/outbound/tel:{$this->senderNumber}/requests";

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "outboundSMSMessageRequest" => [
                    "address" => "tel:{$location->client_phone}",
                    "senderAddress" => "tel:{$this->senderNumber}",
                    "outboundSMSTextMessage" => [
                        "message" => $message
                    ]
                ]
            ]
        ]);

        if ($response->getStatusCode() == 201 || $response->getStatusCode() == 202) {
            return "✅ SMS envoyé avec succès à {$location->client_name} ({$location->client_phone})";
        }

        return "❌ Erreur lors de l'envoi du SMS.";
    }
}
