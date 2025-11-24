<?php

namespace App\Events;

use App\Models\Produit;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PublicChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ProductCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $produit;

    public function __construct(Produit $produit)
    {
        $this->produit = $produit;
    }

    public function broadcastOn()
    {
        // Channel public pour que les clients abonnés reçoivent la notification en temps réel
        return new Channel('products');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->produit->id,
            'nom' => $this->produit->nom,
            'prix' => $this->produit->prix,
            'categorie' => $this->produit->categorie,
            'image' => $this->produit->image,
            'created_at' => $this->produit->created_at?->toIso8601String(),
        ];
    }
}
