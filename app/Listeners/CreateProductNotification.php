<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateProductNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ProductCreated $event): void
    {
        $produit = $event->produit;

        $title = 'Nouveau produit disponible';
        $message = sprintf('%s est maintenant disponible au prix de %s GNF', $produit->nom, $produit->prix);

        $data = [
            'produit_id' => $produit->id,
            'nom' => $produit->nom,
            'prix' => $produit->prix,
            'categorie' => $produit->categorie,
            'image' => $produit->image,
        ];

        // Notifier tous les clients
        $clients = User::where('role', 'client')->get();
        foreach ($clients as $client) {
            Notification::create([
                'user_id' => $client->id,
                'type' => 'product',
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }

        // Notifier les admins aussi
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'product',
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }
    }
}
