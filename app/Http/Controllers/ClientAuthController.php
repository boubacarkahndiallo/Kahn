<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Traits\PhoneNumberValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientAuthController extends Controller
{
    use PhoneNumberValidator;

    public function checkClient(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'tel' => 'required|string'
            ]);

            $tel = $request->input('tel');

            // Valider et formater le numéro
            if (!$this->validatePhoneNumber($tel)) {
                return response()->json([
                    'exists' => false,
                    'error' => 'Numéro de téléphone invalide'
                ], 422);
            }

            // Formater le numéro pour la recherche
            $formattedTel = $this->formatPhoneNumber($tel);
            Log::info('Vérification du client avec le numéro: ' . $formattedTel);

            // Rechercher le client avec le numéro formaté (maintenant tous les numéros sont normalisés en E164)
            $client = Client::where('tel', $formattedTel)->first();

            if ($client) {
                Log::info('Client trouvé: ' . $client->id . ' (numéro: ' . $formattedTel . ')');
                return response()->json([
                    'exists' => true,
                    'clientInfo' => [
                        'id' => $client->id,
                        'nom' => $client->nom,
                        'tel' => $client->tel,
                        'whatsapp' => $client->whatsapp,
                        'adresse' => $client->adresse
                    ]
                ]);
            }
            Log::info('Client non trouvé pour le numéro: ' . $formattedTel);
            return response()->json([
                'exists' => false,
                'message' => 'Ce numéro n\'est pas enregistré'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du client: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Une erreur est survenue lors de la vérification'
            ], 500);
        }
    }
}
