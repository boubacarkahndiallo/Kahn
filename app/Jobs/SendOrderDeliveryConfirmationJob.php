<?php

namespace App\Jobs;

use App\Models\Commande;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderDeliveryConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $commandeId;

    /**
     * Create a new job instance.
     *
     * @param int $commandeId
     */
    public function __construct(int $commandeId)
    {
        $this->commandeId = $commandeId;
    }

    /**
     * Execute the job.
     *
     * @param WhatsAppService $whatsapp
     * @return void
     */
    public function handle(WhatsAppService $whatsapp): void
    {
        $commande = Commande::with('client')->find($this->commandeId);
        if (!$commande || !$commande->client) {
            Log::warning("SendOrderDeliveryConfirmationJob: commande or client not found (id={$this->commandeId})");
            return;
        }

        // Choisir le numéro WhatsApp si disponible, sinon le téléphone
        $to = $commande->client->whatsapp ?? $commande->client->tel ?? null;
        if (empty($to)) {
            Log::warning("SendOrderDeliveryConfirmationJob: aucun numéro disponible pour la commande {$commande->id}");
            return;
        }

        // Message en français — simple et clair
        $clientNom = $commande->client->nom ?? '';
        $numero = $commande->numero_commande ?? $commande->id;
        $message = "Bonjour {$clientNom},\n\nNous espérons que vous allez bien. Il y a environ 1 heure nous avons expédié votre commande {$numero}. Pouvez-vous confirmer si vous avez bien reçu votre commande ?\n\nRépondez OUI si vous avez reçu, NON si vous ne l'avez pas reçue. Merci !";

        $sent = $whatsapp->sendMessage($to, $message);

        if ($sent) {
            // Marquer que la demande de confirmation a été envoyée
            $commande->confirmation_requested_at = now();
            $commande->save();
            Log::info("Confirmation request WhatsApp envoyée pour commande {$commande->id}");
        } else {
            Log::error("Échec envoi WhatsApp pour commande {$commande->id}");
        }
    }
}
