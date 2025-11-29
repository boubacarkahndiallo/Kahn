<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderCreated $event): void
    {
        // Récupérer tous les admins et super admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

        $commande = $event->commande;
        $client = $commande->client;

        $title = 'Nouvelle commande reçue';
        $message = sprintf(
            'Commande #%s de %s - Total: %s Fcfa',
            $commande->numero_commande,
            $client?->nom ?? 'Client inconnu',
            $commande->prix_total
        );

        $data = [
            'commande_id' => $commande->id,
            'numero_commande' => $commande->numero_commande,
            'client_id' => $commande->client_id,
            'client_nom' => $client?->nom,
            'client_tel' => $client?->tel,
            'prix_total' => $commande->prix_total,
            'produits' => $commande->produits,
            'date_commande' => $commande->date_commande?->toIso8601String(),
        ];

        // NOTE: The controller now creates DB notifications synchronously so users
        // can see them immediately. This listener is kept as a queued handler for
        // any side-effects required (emails, logs). We'll log the event here.
        logger()->info('CreateOrderNotification processed for commande id: ' . $commande->id);
    }
}
