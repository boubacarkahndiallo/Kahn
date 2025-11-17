@extends('base')

@section('title', 'Commandes')

@section('content')
    <div class="all-title-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Gestion des Commandes</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('app_accueil') }}" style="color:#070a23;">Accueil</a>
                        </li>
                        <li class="breadcrumb-item active" style="color:white;">Commandes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Flash -->
    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Header + Boutons -->
    <div class="container mt-4">
        <div class="row align-items-center mb-4 p-3 rounded shadow-sm"
            style="background:white; border-left:5px solid #1c911e;">
            <div class="col-md-4 d-flex align-items-center gap-2">
                <i class="fa fa-shopping-cart fa-2x" style="color:#070a23;"></i>
                <h2 class="mb-0 fw-bold" style="color:#070a23;">Liste des Commandes</h2>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher..." value="">
                    <button class="btn btn-clear-search" type="button" style="background:#1c911e; color:white;">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn fw-bold shadow-sm" style="background:#1c911e; color:white;"
                    data-bs-toggle="modal" data-bs-target="#ajoutCommandeModal">
                    <i class="fa fa-plus me-1"></i> Ajouter une commande
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Commande -->
    <div class="modal fade" id="ajoutCommandeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background:#1c911e;">
                    <h5 class="modal-title text-white"><i class="fa fa-plus me-2"></i> Nouvelle Commande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="commandeForm" method="POST" action="{{ route('commandes.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div id="commandeErrors" class="alert alert-danger d-none"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Client</label>
                                <select name="client_id" id="clientSelect" class="form-select" required>
                                    <option value="" selected>-- Sélectionner un client --</option>
                                    @foreach (\App\Models\Client::orderBy('nom')->get() as $client)
                                        <option value="{{ $client->id }}">{{ $client->nom }}@if (!empty($client->tel) || !empty($client->telephone))
                                                - {{ $client->tel ?? $client->telephone }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Statut</label>
                                <select name="statut" class="form-select" required>
                                    <option value="en_cours" selected>En cours</option>
                                    <option value="livree">Livrée</option>
                                    <option value="annulee">Annulée</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-center mt-3" style="color:#1c911e;">Produits</h6>
                        @php $produits = \App\Models\Produit::orderBy('nom')->get(); @endphp
                        <table class="table table-sm text-center align-middle mt-2">
                            <thead style="background:#1c911e; color:white;">
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix (GNF)</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="produitsTableBody">
                                <tr>
                                    <td>
                                        <select name="produits[0][nom]" class="form-select produitSelect" required>
                                            <option value="" disabled selected>-- Choisir --</option>
                                            @foreach ($produits as $produit)
                                                <option value="{{ $produit->nom }}" data-prix="{{ $produit->prix }}">
                                                    {{ $produit->nom }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="produits[0][quantite]"
                                            class="form-control quantiteInput" value="1" min="1" required></td>
                                    <td><input type="number" name="produits[0][prix]" class="form-control prixInput"
                                            readonly></td>
                                    <td class="totalProduit fw-bold text-success">0</td>
                                    <td><button type="button" class="btn btn-danger btn-sm removeRow"><i
                                                class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-end">
                            <button type="button" id="addProduct" class="btn btn-sm"
                                style="background:#070a23; color:white;">
                                <i class="fa fa-plus me-1"></i> Ajouter un produit
                            </button>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Prix total</label>
                                <input type="number" name="prix_total" id="prix_total"
                                    class="form-control fw-bold text-success" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date commande</label>
                                <input type="date" name="date_commande" class="form-control"
                                    value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn" style="background:#070a23; color:white;"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn" style="background:#1c911e; color:white;">
                            <i class="fa fa-save me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tableau Commandes -->
    <div class="container-fluid px-4 mt-4">
        <div class="table-main table-responsive rounded shadow-sm" style="overflow-x:auto;">
            <table class="table align-middle text-center mb-0 w-100">
                <thead style="background:#1c911e; color:white;">
                    <tr>
                        <th>#</th>
                        <th>Numéro</th>
                        <th>Client</th>
                        <th>Produits</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableCommandes">
                    @forelse ($commandes as $key => $commande)
                        <tr data-commande-id="{{ $commande->id }}">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $commande->numero_commande }}</td>
                            <td>{{ $commande->client->nom }}</td>
                            <td>
                                @if (is_array($commande->produits))
                                    <div class="text-start" style="max-height: 100px; overflow-y: auto;">
                                        @foreach ($commande->produits as $produit)
                                            @php
                                                $nom = $produit['nom'] ?? ($produit['name'] ?? 'Produit');
                                                $qty =
                                                    $produit['quantite'] ??
                                                    ($produit['qty'] ?? ($produit['quantity'] ?? 1));
                                                $prix = $produit['prix'] ?? ($produit['price'] ?? 0);
                                                $total = $prix * $qty;
                                            @endphp
                                            <div class="mb-1">
                                                {{ $nom }} (x{{ $qty }}) -
                                                {{ number_format($prix, 0, ',', ' ') }} GNF
                                                <span class="text-success">=
                                                    {{ number_format($total, 0, ',', ' ') }} GNF
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div>Aucun produit</div>
                                @endif
                            </td>
                            <td>{{ number_format($commande->prix_total, 0, ',', ' ') }} GNF</td>
                            <td>
                                @if ($commande->statut === 'en_cours')
                                    <span class="fw-bold text-warning">En cours</span>
                                @elseif($commande->statut === 'livree')
                                    <span class="fw-bold text-success">Livrée</span>
                                @else
                                    <span class="fw-bold text-danger">Annulée</span>
                                @endif
                            </td>
                            <td>{{ $commande->date_commande->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-voir" style="color:#070a23;"
                                        data-id="{{ $commande->id }}" title="Voir">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-modifier" style="color:#1c911e;"
                                        data-id="{{ $commande->id }}" title="Modifier">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('commandes.destroy', $commande->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="color:red;"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Aucune commande trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

<!-- Modal Voir -->
<div class="modal fade" id="voirCommandeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1c911e;">
                <h5 class="modal-title text-white"><i class="fa fa-eye me-2"></i> Détails de la Commande</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <p><strong>Numéro :</strong> <span id="voir-numero"></span></p>
                        <p><strong>Client :</strong> <span id="voir-client"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date :</strong> <span id="voir-date"></span></p>
                        <p><strong>Statut :</strong> <span id="voir-statut"></span></p>
                    </div>
                </div>

                <hr>
                <h6 class="fw-bold text-center" style="color:#1c911e;">Produits</h6>
                <div class="table-responsive mt-3">
                    <table class="table table-sm text-center align-middle w-100">
                        <thead style="background:#1c911e; color:white;">
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix (GNF)</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="voir-produits"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total :</th>
                                <th class="text-center" id="voir-total"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn" data-bs-dismiss="modal"
                    style="background:#070a23; color:white;">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modifier -->
<div class="modal fade" id="modifierCommandeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1c911e;">
                <h5 class="modal-title text-white"><i class="fa fa-edit me-2"></i> Modifier la Commande</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="modifierCommandeForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-commande-id" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Client</label>
                            <input type="text" id="edit-client" class="form-control" readonly>
                            <input type="hidden" id="edit-client-id" name="client_id">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Statut</label>
                            <select name="statut" id="edit-statut" class="form-select" required>
                                <option value="en_cours">En cours</option>
                                <option value="livree">Livrée</option>
                                <option value="annulee">Annulée</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-bold text-center mt-3" style="color:#1c911e;">Produits</h6>
                    <div class="table-responsive">
                        <table class="table table-sm text-center align-middle mt-2 w-100">
                            <thead style="background:#1c911e; color:white;">
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix (GNF)</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="edit-produits"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total :</th>
                                    <th class="text-center" id="edit-total">0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn" style="background:#070a23; color:white;"
                        data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn" style="background:#1c911e; color:white;">
                        <i class="fa fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/commande.js') }}"></script>
    <script src="{{ asset('js/search-sort.js') }}"></script>
    <script src="{{ asset('js/modales-commandes.js') }}"></script>
@endpush
