<!-- resources/views/components/mes-commandes.blade.php -->
<div class="mes-commandes">
    <h5>Mes commandes récentes</h5>
    <div class="list-group">
        @forelse($commandes as $commande)
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">{{ $commande->numero_commande }}</h6>
                    <small>{{ $commande->date_commande_format }}</small>
                </div>
                <p class="mb-1">
                    {{ $commande->produits_description }}
                </p>
                <small class="text-muted">Total : {{ $commande->total_format }}</small>
                <span
                    class="badge {{ $commande->statut === 'en_cours' ? 'bg-warning' : ($commande->statut === 'livree' ? 'bg-success' : 'bg-danger') }}">
                    {{ ucfirst($commande->statut) }}
                </span>
            </div>
        @empty
            <div class="text-muted text-center py-3">
                Aucune commande pour le moment
            </div>
        @endforelse
    </div>
</div>
