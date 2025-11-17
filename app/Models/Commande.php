<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Commande extends Model
{
    // Nom explicite de la table
    protected $table = 'commandes';

    // Colonnes autorisées à être remplies
    protected $fillable = [
        'numero_commande',
        'client_id',
        'client_order_uuid',
        'produits',      // JSON contenant les produits commandés
        'prix_total',
        'statut',
        'date_commande',
        'confirmation_requested_at',
        'delivery_confirmed',
        'delivery_confirmed_at',
    ];

    // Conversion automatique JSON ↔ array et formatage date
    protected $casts = [
        'produits' => 'array',
        'date_commande' => 'datetime',
        'client_order_uuid' => 'string',
        'confirmation_requested_at' => 'datetime',
        'delivery_confirmed_at' => 'datetime',
        'delivery_confirmed' => 'boolean',
    ];

    /**
     * 🔗 Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * ⚙️ Valeurs par défaut et génération automatique
     */
    protected static function booted()
    {
        static::creating(function ($commande) {
            // Générer un numéro unique si vide
            if (empty($commande->numero_commande)) {
                $commande->numero_commande = 'MD-' . now()->format('Y') . '-' . strtoupper(Str::random(4));
            }

            // Statut par défaut
            if (empty($commande->statut)) {
                $commande->statut = 'en_cours';
            }

            // Date commande par défaut
            if (empty($commande->date_commande)) {
                $commande->date_commande = now();
            }
        });
    }

    /**
     * 💰 Retourne le total formaté (ex : 125 000 GNF)
     */
    public function getTotalFormatAttribute()
    {
        return number_format($this->prix_total, 0, ',', ' ') . ' GNF';
    }

    /**
     * 📅 Retourne la date formatée (ex : 18/10/2025 à 14:32)
     */
    public function getDateCommandeFormatAttribute()
    {
        return $this->date_commande ? $this->date_commande->format('d/m/Y H:i') : '';
    }

    /**
     * 🧾 Génère un texte lisible du contenu de la commande
     */
    public function getProduitsDescriptionAttribute()
    {
        if (!$this->produits || !is_array($this->produits)) {
            return 'Aucun produit enregistré';
        }

        $description = '';
        foreach ($this->produits as $p) {
            $nom = $p['nom'] ?? 'Produit';
            $qty = $p['qty'] ?? 1;
            $prix = number_format($p['prix'] ?? 0, 0, ',', ' ');
            $total = number_format($p['total'] ?? 0, 0, ',', ' ');
            $description .= "- {$nom} ({$qty} x {$prix} GNF = {$total} GNF)\n";
        }
        return $description;
    }
}
