<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nom',
        'tel',
        'whatsapp',
        'adresse',
        'image',
        'statut',
        'description',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    // On désactive la gestion du mot de passe pour Auth
    public function getAuthPassword()
    {
        return null;
    }
}
