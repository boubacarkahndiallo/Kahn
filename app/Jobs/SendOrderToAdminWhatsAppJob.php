<?php

namespace App\Jobs;

use App\Models\Commande;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderToAdminWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $commandeId;

    public function __construct($commandeId)
    {
        $this->commandeId = $commandeId;
    }

    public function handle(WhatsAppService $whatsApp)
    {
        try {
            $commande = Commande::with('client')->find($this->commandeId);
            if (!$commande) {
                return;
            }

            $client = $commande->client;
            $produitsList = collect($commande->produits)->map(function ($p) {
                return ($p['nom'] ?? '') . ' x' . ($p['qty'] ?? 1) . ' (' . ($p['prix'] ?? 0) . ' GNF)';
            })->implode(', ');

<<<<<<< HEAD
<<<<<<< HEAD
            // Use configured admin number or fallback to env variable or default
            $adminNumber = config('services.admin.whatsapp_number') ?? env('ADMIN_WHATSAPP_NUMBER', '+224623248567');
=======
            $adminNumber = config('services.admin.whatsapp_number') ?? env('ADMIN_WHATSAPP_NUMBER', null);
>>>>>>> 34c44d8 (Initial commit)
=======
            $adminNumber = config('services.admin.whatsapp_number') ?? env('ADMIN_WHATSAPP_NUMBER', null);
>>>>>>> 34c44d8 (Initial commit)
            if (!$adminNumber) {
                logger()->warning('SendOrderToAdminWhatsAppJob: admin number not configured');
                return;
            }

            $message = "Nouvelle commande reçue !\n" .
                "Numéro : " . $commande->numero_commande . "\n" .
                "Client : " . ($client->nom ?? 'Inconnu') . "\n" .
                "Téléphone : " . ($client->tel ?? '-') . "\n" .
                "Produits : " . $produitsList . "\n" .
                "Total : " . $commande->prix_total . " GNF";

            $whatsApp->sendMessage($adminNumber, $message);
        } catch (\Throwable $e) {
            logger()->error('SendOrderToAdminWhatsAppJob failed: ' . $e->getMessage());
        }
    }
}
