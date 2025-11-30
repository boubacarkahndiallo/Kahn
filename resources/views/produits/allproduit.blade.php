@extends('base')

@section('title', 'Nos Produits')

@section('content')
    <div class="all-title-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Nos Produits</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('app_accueil') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Produit</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">

        <!-- Header + Filtre/Recherche -->
        <div class="row align-items-center mb-4 p-3 products-header">
            <div class="col-md-4 col-12 d-flex align-items-center gap-2">
                <i class="fa fa-shopping-cart fa-2x text-success" style="color:#1c911e;"></i>
                <div>
                    <h2 class="mb-0 fw-bold">Cochez les produits à commander</h2>
                    <small class="product-count">{{ $produits->count() }} produits disponibles</small>
                </div>
            </div>
            <div class="col-md-8 col-12 d-flex gap-3 justify-content-end flex-wrap filter-bar">
                <!-- Toggle vue (liste / grille) -->
                <div class="d-flex flex-column toggle-area">
                    <label class="form-label fw-semibold mb-1 d-block">Afficher </label>
                    <div class="btn-group" role="group" aria-label="Afficher en liste ou grille">
                        <button type="button" class="btn btn-outline-success btn-sm" id="view-list" aria-pressed="true"><i
                                class="fa fa-bars"></i></button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="view-grid" aria-pressed="false"><i
                                class="fa fa-th-large"></i></button>
                    </div>
                </div>
                <!-- Filtrer par catégorie -->
                <div>
                    <label for="categorie-filter" class="form-label fw-semibold mb-1 d-block">Catégorie :</label>
                    <select id="categorie-filter" class="form-select form-select-sm shadow-sm"
                        aria-label="Filtrer par catégorie">
                        <option value="">Toutes</option>
                        @foreach ($categories as $categorie)
                            <option value="{{ $categorie }}">{{ $categorie }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Recherche -->
                <div>
                    <label for="search-produit" class="form-label fw-semibold mb-1 d-block">Recherche
                        :</label>
                    <input type="text" id="search-produit" class="form-control form-control-sm shadow-sm"
                        placeholder="Tapez pour rechercher..." aria-label="Rechercher un produit">
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <div id="clientInfo" class="d-none"></div>

            <form id="clientRegistrationForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <!-- Alert d'inscription (affichée en haut du formulaire) -->
                <div class="alert alert-danger d-none" id="registration-error"></div>

                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <input type="text" id="nom" class="form-control" name="nom" required
                            placeholder="Nom du restaurant/Hôtel" value="{{ old('nom') }}">
                    </div>
                    <div class="col-6 col-md-3 ">
                        <div class="input-group">
                            <span class="input-group-text phone-flag" aria-hidden="false" role="button"
                                aria-label="Changer le pays" tabindex="0"></span>
                            <input type="tel" class="form-control" id="tel" name="tel" required
                                placeholder="Téléphone" value="{{ old('tel') }}" inputmode="tel" autocomplete="tel"
                                aria-describedby="tel-feedback">
                            <div class="invalid-feedback" id="tel-feedback"></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="input-group">
                            <span class="input-group-text phone-flag" aria-hidden="false" role="button"
                                aria-label="Changer le pays" tabindex="0"></span>
                            <input type="tel" class="form-control" name="whatsapp" id="whatsapp" placeholder="WhatsApp"
                                value="{{ old('whatsapp') }}" inputmode="tel" autocomplete="tel"
                                aria-describedby="whatsapp-feedback">
                            <div class="invalid-feedback" id="whatsapp-feedback"></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="text" id="adresse" class="form-control" name="adresse" required
                            placeholder="Adresse" value="{{ old('adresse') }}">
                    </div>
                    <input type="hidden" name="role" value="client">
                    <input type="hidden" name="statut" value="actif">
                </div>
                <div class="mt-2 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>

        @include('auth.includes.modals')

        @push('scripts')
            <script src="{{ asset('js/produit-auth.js') }}"></script>
            <script src="{{ asset('js/clientRegistration.js') }}"></script>
            <script src="{{ asset('js/client-auth.js') }}"></script>
        @endpush
        <!-- Tableau Produits -->
        <div class="table-main table-responsive">
            <table class="table table-hover align-middle w-100 product-table" id="produits-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" aria-label="Tout sélectionner"></th>
                        <th>Produits</th>
                        <th>P.U</th>
                        <th>Quantités</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produits as $produit)
                        <tr data-id="{{ $produit->id }}" data-prix="{{ $produit->prix }}"
                            data-categorie="{{ $produit->categorie }}">
                            <td><input type="checkbox" class="select-produit"></td>
                            <td class="d-flex align-items-center gap-2">
                                @if ($produit->image)
                                    <img src="{{ $produit->image ? asset('storage/' . $produit->image) : asset('images/logo1.png') }}"
                                        class="product-thumbnail" alt="{{ $produit->nom }}">
                                @else
                                    <img src="https://via.placeholder.com/60" alt="Pas d'image"
                                        class="product-thumbnail">
                                @endif
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $produit->nom }}</span>
                                    @if ($produit->categorie)
                                        <span
                                            class="badge bg-light text-success small mt-1">{{ $produit->categorie }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ number_format($produit->prix, 0, ',', ' ') }}</td>
                            <td>
                                <input type="number" class="form-control qty" min="1" value="1" disabled>
                            </td>
                            <td class="total">{{ number_format($produit->prix, 0, ',', ' ') }}</td>
                            <td>
                                <button class="btn btn-danger btn-annuler d-none" title="Annuler">&times;</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Vue Grille (cards) - initialement masquée -->
        <div id="products-grid" class="product-grid row g-3 d-none" aria-hidden="true"></div>

        <!-- Résumé total et bouton Commander -->
        <div class="d-flex justify-content-end align-items-center mt-3 gap-3">
            <div class="fw-bold fs-5">
                Total : <span id="grand-total" aria-live="polite">0</span> GNF
            </div>
            <button id="btn-commander" class="btn btn-success btn-lg btn-commander btn-pulse" title="Commandez"
                aria-label="Cliquez pour confirmer et passer votre commande" disabled>
                <i class="fa fa-check-circle me-2"></i>
                Commandez
                <span class="visually-hidden">. Confirmer la commande</span>
            </button>
        </div>

    </div>

    <!-- Modal Facture -->
    <div class="modal fade" id="factureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title text-white"><i class="fa fa-file-invoice me-2"></i> Facture de commande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- En-tête de la facture avec logo et informations de l'entreprise -->
                    <div class="bg-light p-4 rounded-3 mb-4">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <img src="{{ asset('images/logo.png') }}" alt="Mourima Market Logo"
                                    style="max-width: 150px;" class="img-fluid mb-2">
                            </div>
                            <div class="col-md-9">
                                <h3 class="fw-bold mb-3 text-success">Mourima Market</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-success"></i>Nongo</p>
                                        <p class="mb-2"><i
                                                class="fas fa-envelope me-2 text-success"></i>mourima.enterprise@gmail.com
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><i class="fas fa-phone me-2 text-success"></i>623 24 85 67 | 628
                                            27 53 29</p>
                                        <p class="mb-2"><i class="fab fa-whatsapp me-2 text-success"></i>623 24 85 67
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations client et commande -->
                    <div class="row mb-4">
                        <div class="col-md-6 p-3 border-end">
                            <h5 class="fw-bold mb-3 text-success">Information Client</h5>
                            <div id="facture-client-info" class="ps-2"></div>
                        </div>
                        <div class="col-md-6 p-3">
                            <h5 class="fw-bold mb-3 text-success">Détails de la Commande</h5>
                            <div class="ps-2">
                                <p class="mb-2"><strong>N° Commande :</strong> <span id="facture-numero"
                                        class="ms-2"></span></p>
                                <p class="mb-2"><strong>Date :</strong> <span id="facture-date" class="ms-2"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm text-center align-middle w-100 table-bordered">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th class="py-2">Produit</th>
                                    <th class="py-2" style="width: 120px;">Quantité</th>
                                    <th class="py-2" style="width: 150px;">Prix unitaire</th>
                                    <th class="py-2" style="width: 150px;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="facture-produits"></tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-end fw-bold py-3">Total :</td>
                                    <td class="text-end fw-bold py-3" id="facture-total"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="alert alert-success mt-4 border-success">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fa-2x"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Confirmation de commande</h6>
                                <p class="mb-0">Un agent vous contactera dans quelques minutes pour confirmer
                                    votre commande.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center py-3">
                    <div class="btn-group">
                        <a id="btn-call" class="btn btn-outline-success" href="tel:+224623248567">
                            <i class="fas fa-phone-alt me-2"></i>Nous appeler
                        </a>
                        <a id="btn-whatsapp" class="btn btn-success" target="_blank" rel="noopener"
                            title="Envoyer la commande sur WhatsApp">
                            <i class="fab fa-whatsapp me-1"></i> WhatsApp
                        </a>
                        <button type="button" class="btn btn-outline-secondary" id="btn-share"
                            title="Partager la facture">
                            <i class="fa fa-share-alt me-1"></i> Partager
                        </button>
                    </div>

                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Fermer
                        </button>
                        <button type="button" class="btn btn-success" id="btn-imprimer-facture"
                            aria-label="Imprimer la facture">
                            <i class="fa fa-print me-1"></i> Imprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast (confirmation de copie) -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed" style="z-index: 1080; right: 1rem; bottom: 1rem;">
        <div id="factureToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Facture</strong>
                <small>Now</small>
                <button type="button" class="btn-close ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="factureToastBody">
                Texte copié dans le presse-papiers.
            </div>
        </div>
    </div>

@endsection
