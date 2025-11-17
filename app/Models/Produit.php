<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        'nom', 'description', 'prix', 'categorie', 'image', 'stock'
    ];

    // Accessor pour générer le statut dynamiquement
public function getStatutAttribute(): string
{
    if ($this->stock >= 0 && $this->stock <= 5) {
        return 'Rupture';
    } elseif ($this->stock > 5 && $this->stock <= 10) {
        return 'Presque Fini';
    } elseif($this->stock >10) {
        return 'Disponible';
    }
    else {
        return 'Erreur';
    }
}

}
