<!-- resources/views/components/client-info.blade.php -->
<div class="client-info">
    @auth
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Mon profil</h5>
                <ul class="list-unstyled">
                    <li><strong>Nom :</strong> {{ auth()->user()->nom }}</li>
                    <li><strong>Téléphone :</strong> {{ auth()->user()->tel }}</li>
                    <li><strong>WhatsApp :</strong> {{ auth()->user()->whatsapp ?? '—' }}</li>
                    <li><strong>Adresse :</strong> {{ auth()->user()->adresse }}</li>
                    <li><strong>Statut :</strong> <span class="badge bg-success">{{ auth()->user()->statut }}</span></li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('clients.edit', auth()->id()) }}" class="btn btn-sm btn-primary w-100">
                        <i class="fa fa-edit"></i> Modifier mes informations
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted mb-0">Connectez-vous pour voir vos informations</p>
            </div>
        </div>
    @endauth
</div>
