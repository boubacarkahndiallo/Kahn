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

        // NOTE: Notifications are created synchronously by the controller on create so
        // that they are immediately available to users. This listener is kept for
        // additional side-effects (email, logs, third-party integrations) if needed.
        // For now, we simply log the creation.
        logger()->info('CreateProductNotification handled for product id: ' . $produit->id);
    }
}
