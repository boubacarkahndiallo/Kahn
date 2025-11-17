<?php

namespace App\Events;

use App\Models\Commande;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $commande;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('admin-orders');
    }

    /**
     * Data to broadcast
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->commande->id,
            'numero' => $this->commande->numero_commande,
            'prix_total' => $this->commande->prix_total,
            'client_nom' => optional($this->commande->client)->nom,
            'date' => $this->commande->date_commande ? $this->commande->date_commande->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
        ];
    }
}
