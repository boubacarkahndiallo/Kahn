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

        // Créer une notification pour chaque admin
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'order',
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }

        // Créer une notification pour le client (confirmation pour le client lui-même)
        if ($client) {
            Notification::create([
                'user_id' => $client->id,
                'type' => 'order',
                'title' => 'Votre commande a été reçue',
                'message' => 'Votre commande ' . $commande->numero_commande . ' a bien été enregistrée. Nous vous contacterons sous peu.',
                'data' => $data,
            ]);
        }
    }
}
