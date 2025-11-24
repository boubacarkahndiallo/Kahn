<?php

use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channel Routes
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('admin-orders', function ($user) {
    // Autoriser uniquement les administrateurs
    return $user && in_array($user->role ?? '', ['admin', 'super_admin']);
});

// Canal privé pour notifications utilisateur (lecture par l'utilisateur concerné uniquement)
Broadcast::channel('user.{id}.notifications', function ($user, $id) {
    return $user && ((int) $user->id === (int) $id);
});
