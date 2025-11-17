<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Les attributs pouvant être assignés en masse.
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'tel',
        'whatsapp',
        'adresse',
        'password',
        'role',
        'photo',
        'actifs',
        'is_verified',
        'has_set_password',
        'first_login_token',
        'activation_code',
        'activation_token',
        'email_verified_at',
    ];

    /**
     * Les attributs cachés lors de la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_code',
        'activation_token',
        'first_login_token',
    ];

    /**
     * Les attributs castés automatiquement.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'has_set_password' => 'boolean',
    ];

    /**
     * Vérifie si l’utilisateur est Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Vérifie si l’utilisateur est Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l’utilisateur est un simple utilisateur.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
