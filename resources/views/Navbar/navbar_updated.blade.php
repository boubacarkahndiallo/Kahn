<!-- resources/views/layouts/header.blade.php -->

<!-- Start Main Top -->
<div class="main-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                {{-- <div class="custom-select-box">
                    <select id="currency" class="selectpicker show-tick form-control" data-placeholder="GNF">
                        <option>GNF</option>
                        <option>FCFA</option>
                        <option>USD</option>
                        <option>EURO</option>
                    </select>
                </div> --}}
                <div class="right-phone-box">
                    <p>Tél. : <a href="tel:+224623248567"> +224 623 24 85 67</a></p>
                </div>
                <div class="our-link">
                    <ul>
                        @if (auth()->check())
                            <li><a href="{{ route('app_dashboard') }}"><i class="fas fa-tachometer-alt"></i> Tableau de
                                    bord</a></li>
                            <li>
                                <!-- Logout form: submit via POST to avoid GET logout issues -->
                                <form id="logout-form" action="{{ route('app_logout') }}" method="POST"
                                    style="display:none;">
                                    @csrf
                                </form>
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </a>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}" style="color: #1c911e;"><i
                                        class="fas fa-sign-in-alt"></i> Se connecter</a></li>
                        @endif
                        <li><a href="{{ route('app_contact') }}"><i class="fas fa-headset"></i> Contactez-nous</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                {{-- <div class="login-box">
                    <select id="user-action" class="selectpicker show-tick form-control" data-placeholder="Connexion">
                        @if (auth()->check() && auth()->user()->role == 'client')
                            <option>Déconnexion</option>
                        @else
                            <option>S'inscrire</option>
                        @endif
                    </select>
                </div> --}}
                <div class="text-slid-box">
                    <div id="offer-box" class="carouselTicker">
                        <ul class="offer-box">
                            <li><i class="fab fa-opencart"></i> Des produits agricoles 100% frais</li>
                            <li><i class="fab fa-opencart"></i> Variété de produits pour répondre à vos besoins</li>
                            <li><i class="fab fa-opencart"></i> Commandez et faites-vous livrer rapidement</li>
                            <li><i class="fab fa-opencart"></i> Soutenons les producteurs locaux</li>
                            <li><i class="fab fa-opencart"></i> L'agriculture au service de votre assiette</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Main Top -->

<header class="main-header">
    <!-- Start Navigation -->
    <style>
        /* Align menu links to the right on large screens while keeping the logo on the left */
        @media (min-width: 992px) {

            /* Make the navbar container a flex row so we can position brand, menu and extras */
            .navbar .container {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            /* Give the collapse area available space and align its items to the right */
            #navbar-menu {
                display: flex !important;
                flex: 1 1 auto;
                justify-content: flex-end;
                align-items: center;
            }

            /* Ensure the ul nav items render horizontally */
            #navbar-menu .navbar-nav {
                display: flex;
                gap: 0.75rem;
                align-items: center;
            }
        }

        /* Desktop (min-width: 992px): Hide brand text, show only logo */
        @media (min-width: 992px) {
            nav.navbar .navbar-brand .brand-text {
                display: none;
            }
        }

        /* Mobile (max-width: 991px): [☰] [Logo] Mourima Market */
        @media (max-width: 991px) {
            #navbar-menu {
                width: auto;
            }

            /* Center the navbar header on mobile */
            nav.navbar .navbar-header {
                position: relative;
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 1rem;
                gap: 0.5rem;
            }

            /* Adjust toggle button position to the left */
            nav.navbar .navbar-toggler {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                order: -1;
            }

            /* Make the brand a flex container with logo then text */
            nav.navbar .navbar-brand {
                margin: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.6rem !important;
                padding: 0 !important;
                flex-direction: row !important;
                width: auto;
                height: auto;
                order: 0;
            }

            /* Logo size */
            nav.navbar .navbar-brand img.logo {
                width: 45px !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                flex-shrink: 0;
            }

            /* "Mourima Market" text - visible on mobile */
            nav.navbar .navbar-brand .brand-text {
                display: block;
                font-size: 1.3rem;
                font-weight: 800;
                color: #1c911e;
                letter-spacing: 0.5px;
                white-space: nowrap;
                margin: 0;
                line-height: 1.2;
                flex-shrink: 0;
            }
        }
    </style>
    <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-default bootsnav">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu"
                    aria-controls="navbars-rs-food" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="{{ route('app_accueil') }}">
                    <img src="{{ asset('images/logo1.png') }}"
                        srcset="{{ asset('images/logo1.png') }} 1x, {{ asset('images/logo1.jpg') }} 2x" class="logo"
                        alt="Logo">
                    <span class="brand-text">Mourima Market</span>
                </a>
            </div>

            <div class="collapse navbar-collapse" id="navbar-menu">
                <ul class="nav navbar-nav ml-auto" data-in="fadeInDown" data-out="fadeOutUp">
                    <li class="nav-item ">
                        <a class="nav-link @if (Request::route()->getName() == 'app_accueil') active @endif" aria-current="page"
                            href="{{ route('app_accueil') }}">Accueil</a>
                    </li>
                    @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Personnel</a>
                            <ul class="dropdown-menu">
                                <li> <a class="nav-link @if (Request::route()->getName() == 'admin.index') active @endif"
                                        aria-current="page" href="{{ route('admin.users.index') }}">Utilisateur</a>
                                </li>
                                <li><a href="{{ route('clients.index') }}">Clients</a></li>
                                <li><a href="#">Fournisseurs</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Produits</a>
                        <ul class="dropdown-menu">
                            @if (auth()->check() && !in_array(auth()->user()->role, ['client', 'fournisseur']))
                                <li><a href="{{ route('produits.index') }}">Ajout produit</a></li>
                            @endif
                            <li><a href="{{ route('produits.allproduit') }}">Nos produits</a></li>
                            {{-- <li><a href="{{ route('app_legumes') }}">Légumes</a></li>
                            <li><a href="#">Fruits</a></li>
                            <li><a href="#">Poissons & Poulets</a></li> --}}
                        </ul>
                    </li>

                    @if (auth()->check() && !in_array(auth()->user()->role, ['client', 'fournisseur']))
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Commandes</a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('commandes.index') }}">Commandes</a></li>
                                <li><a href="{{ route('livraisons.index') }}">Livraisons</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ route('app_contact') }}">Contact</a></li>
                </ul>
            </div>

            <div class="attr-nav">
                <ul>
                    <li class="search"><a href="#"><i class="fa fa-search"></i></a></li>

                    <li class="side-moi">
                        <a href="#">
                            <i class="fa fa-user" id="btn-moi"></i>
                            {{-- <p>Moi</p> --}}
                        </a>
                    </li>

                    <li class="side-menu">
                        <a href="#">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="badge bg-success" id="cart-count">0</span>
                            {{-- <p>Panier</p> --}}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Panier -->
        <div class="side" id="side-cart">
            <a href="#" class="close-side"><i class="fa fa-times"></i></a>
            <li class="cart-box">
                <ul class="cart-list">
                    <li class="total">
                        <a href="#" class="btn btn-default hvr-hover btn-cart">Voir</a>
                        <span class="float-right"><strong>Total</strong>: 0 GNF</span>
                    </li>
                </ul>
            </li>
        </div>
        <!-- End Panier -->

        <!-- Panneau "Moi" -->
        <div class="side" id="side-moi">
            <a href="#" class="close-side"><i class="fa fa-times"></i></a>

            <div class="cart-box">
                <ul class="cart-list list-unstyled p-3">
                    <li class="mb-3 d-flex align-items-center justify-content-between">
                        <h5 class="text-success mb-0"><i class="fa fa-user-circle me-2"></i> Mes informations</h5>
                        <button type="button" id="btnToggleTheme"
                            class="btn btn-outline-primary btn-sm btn-toggle-theme" aria-label="Basculer le thème"
                            style="padding: 0.25rem 0.5rem; border: none; background: none;">
                            <i class="theme-icon fas fa-moon" aria-hidden="true"></i>
                            <span class="theme-label visually-hidden">Mode sombre</span>
                        </button>
                    </li>

                    <li id="client-info-content">
                        @if (auth()->check())
                            <div class="user-info">
                                <div class="text-center mb-3">
                                    @if (auth()->user()->photo)
                                        <img src="{{ Storage::url(auth()->user()->photo) }}" alt="Photo de profil"
                                            class="rounded-circle"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        @php
                                            $fullName = trim(
                                                (auth()->user()->prenom ?? '') . ' ' . (auth()->user()->nom ?? ''),
                                            );
                                            $names = array_filter(explode(' ', $fullName));
                                            if (count($names) >= 2) {
                                                $initials = strtoupper($names[0][0] . $names[count($names) - 1][0]);
                                            } elseif (count($names) === 1) {
                                                $initials = strtoupper($names[0][0]);
                                            } else {
                                                $initials = 'U';
                                            }
                                        @endphp
                                        <div class="default-avatar rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 100px; height: 100px; margin: 0 auto; background-color:#1c911e;">
                                            <span
                                                style="font-size:2rem; font-weight:bold; color:white;">{{ $initials }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-center mb-2">
                                    @php
                                        $fullName = trim(
                                            (auth()->user()->prenom ?? '') . ' ' . (auth()->user()->nom ?? ''),
                                        );
                                    @endphp
                                    <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">
                                        {{ $fullName ?: 'Utilisateur' }}
                                    </h5>
                                    <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
                                </div>

                                <div class="info-list">
                                    <div class="info-item mb-2">
                                        <i class="fas fa-phone-alt text-success"></i>
                                        <span>{{ auth()->user()->tel ?? 'Non renseigné' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fab fa-whatsapp text-success"></i>
                                        <span>{{ auth()->user()->whatsapp ?? 'Non renseigné' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-link">
                                            <i class="fas fa-map-marker-alt text-success"></i>
                                            <span>{{ auth()->user()->adresse ?? 'Non renseignée' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="button" id="btnEditProfile"
                                        class="btn btn-outline-success btn-sm w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#editProfileModal">
                                        <i class="fas fa-user-edit"></i> Modifier mes informations
                                    </button>
                                    <form action="{{ route('app_logout') }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="text-center">
                                <p>Veuillez vous connecter pour voir vos informations</p>
                                {{-- <a href="{{ route('login') }}" class="btn btn-success">Se connecter</a> --}}
                            </div>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
        <!-- ✅ Fin panneau "Moi" -->

        <style>
            .user-info .info-list {
                padding: 15px;
            }

            .info-item {
                padding: 5px;
                margin-bottom: 8px;
                border-radius: 6px;
                transition: all 0.3s ease;
                background-color: rgba(248, 249, 250, 0.5);
            }

            .info-item:hover {
                background-color: #f8f9fa;
                transform: translateX(3px);
            }

            .info-link {
                display: flex;
                align-items: center;
                color: #333;
                text-decoration: none;
                gap: 10px;
            }

            .info-link i {
                font-size: 1.2em;
                width: 24px;
                text-align: center;
            }

            .info-link span {
                flex: 1;
                font-size: 0.9em;
            }

            .btn-outline-success:hover,
            .btn-outline-danger:hover {
                color: white;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sélectionner d'abord les panneaux et boutons (éviter les erreurs de référence)
                const sideCart = document.getElementById('side-cart');
                const btnCart = document.getElementById('cart-link');
                const closeCart = sideCart ? sideCart.querySelector('.close-side') : null;

                // Utiliser l'ID explicite pour éviter de récupérer une icône similaire ailleurs
                const btnMoiIcon = document.getElementById('btn-moi');
                const btnMoi = btnMoiIcon ? btnMoiIcon.closest('a') : null;
                const sideMoi = document.getElementById('side-moi');
                const closeSideMoi = sideMoi ? sideMoi.querySelector('.close-side') : null;

                // Helper pour debug (vous pouvez ouvrir la console pour voir ces logs)
                function debug(msg, obj) {
                    if (window.console && window.console.log) {
                        console.log('[NAVBAR]', msg, obj || '');
                    }
                }

                // Gestionnaire pour le panneau "Moi"
                if (btnMoi) {
                    btnMoi.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debug('click btnMoi', btnMoi);
                        if (sideCart) sideCart.classList.remove('on'); // Ferme le panier si ouvert
                        if (sideMoi) sideMoi.classList.add('on');
                    });
                } else {
                    debug('btnMoi non trouvé');
                }

                if (closeSideMoi) {
                    closeSideMoi.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debug('close sideMoi');
                        if (sideMoi) sideMoi.classList.remove('on');
                    });
                }

                // Gestionnaire pour le panneau "Panier"
                if (btnCart) {
                    btnCart.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debug('click btnCart', btnCart);
                        if (sideMoi) sideMoi.classList.remove('on'); // Ferme le panneau Moi si ouvert
                        if (sideCart) sideCart.classList.add('on');
                    });
                } else {
                    debug('btnCart non trouvé');
                }

                if (closeCart) {
                    closeCart.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debug('close sideCart');
                        if (sideCart) sideCart.classList.remove('on');
                    });
                }
            });
        </script>
    </nav>
</header>

<div class="top-search">
    <div class="container">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="text" id="searchInput" class="form-control"
                placeholder="Recherchez vos produits agricoles...">
            <span class="input-group-addon close-search"><i class="fa fa-times"></i></span>
        </div>
        <div id="searchResults" class="mt-3"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        let timeoutId;

        // Helper: build a robust image URL for product.image coming from the server
        function buildImageUrl(img) {
            const fallback = '{{ asset('images/no-image.png') }}';
            try {
                if (!img) return fallback;
                if (/^\s*(https?:)?\/\//i.test(img) || img.startsWith('data:')) return img;
                if (img.startsWith('/storage')) return window.APP_URL + img;
                if (img.startsWith('storage/')) return window.APP_URL + '/' + img;
                return (window.STORAGE_URL || (window.APP_URL + '/storage')) + '/' + img;
            } catch (e) {
                console && console.warn && console.warn('buildImageUrl failed', e);
                return fallback;
            }
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(timeoutId);

            timeoutId = setTimeout(() => {
                const searchTerm = this.value.trim();
                if (searchTerm.length > 2) {
                    // Afficher un indicateur de chargement
                    searchResults.innerHTML =
                        '<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Recherche en cours...</div>';

                    // Effectuer la recherche via AJAX
                    fetch(`/search?q=${encodeURIComponent(searchTerm)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                const html = data.map(product => {
                                    const imageSrc = buildImageUrl(product.image);
                                    return `
                                <div class="card mb-2">
                                    <div class="card-body d-flex">
                                        <div class="me-3" style="width: 100px; height: 100px;">
                                            <img src="${imageSrc}" alt="${product.nom}"
                                                loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/logo1.png') }}';"
                                                class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <h5 class="card-title">${product.nom}</h5>
                                            <p class="card-text">Prix: ${product.prix} GNF</p>
                                            <a href="{{ route('produits.allproduit') }}?product_id=${product.id}" class="btn btn-sm" style="background:#1c911e; color:white;">Commander</a>
                                        </div>
                                    </div>
                                </div>
                            `;
                                }).join('');
                                searchResults.innerHTML = html;
                            } else {
                                searchResults.innerHTML =
                                    '<div class="alert alert-info">Aucun produit trouvé</div>';
                            }
                        })
                        .catch(error => {
                            searchResults.innerHTML =
                                '<div class="alert alert-danger">Une erreur est survenue</div>';
                            console.error('Erreur de recherche:', error);
                        });
                } else {
                    searchResults.innerHTML = '';
                }
            }, 500); // Délai de 500ms avant de lancer la recherche
        });
    });
</script>
<!-- End Top Search -->

<!-- ✅ Modal Inscription -->
<div class="modal fade" id="inscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1c911e;">
                <h5 class="modal-title text-white"><i class="fa fa-user-plus me-2"></i> Inscription</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="inscriptionForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label fw-bold" for="nom">Nom du restaurant/Hôtel</label>
                            <input type="text" id="nom" class="form-control" name="nom" required
                                autocomplete="nom" autofocus value="{{ old('nom') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="tel">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text phone-flag" aria-hidden="false" role="button"
                                    tabindex="0" aria-label="Changer le pays"></span>
                                <input type="tel" class="form-control" id="tel" name="tel" required
                                    inputmode="tel" autocomplete="tel" aria-describedby="telFeedback_navbar_updated"
                                    value="{{ old('tel') }}">
                                <div class="invalid-feedback" id="telFeedback_navbar_updated"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="whatsapp">WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text phone-flag" aria-hidden="false" role="button"
                                    tabindex="0" aria-label="Changer le pays"></span>
                                <input type="tel" class="form-control" name="whatsapp" id="whatsapp"
                                    inputmode="tel" autocomplete="tel"
                                    aria-describedby="whatsappFeedback_navbar_updated" value="{{ old('whatsapp') }}">
                                <div class="invalid-feedback" id="whatsappFeedback_navbar_updated"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold" for="adresse">Adresse</label>
                            <input type="text" id="adresse" class="form-control" name="adresse" required
                                placeholder="Hamdallaye - Carrefour concasseur" value="{{ old('adresse') }}">
                        </div>
                        <input type="hidden" name="role" value="client">
                        <input type="hidden" name="statut" value="actif">
                        <input type="hidden" id="tel_e164" name="tel_e164" value="{{ old('tel_e164') }}">
                        <input type="hidden" id="tel_country" name="tel_country" value="{{ old('tel_country') }}">
                        <input type="hidden" id="tel_dialcode" name="tel_dialcode"
                            value="{{ old('tel_dialcode') }}">
                        <input type="hidden" id="whatsapp_e164" name="whatsapp_e164"
                            value="{{ old('whatsapp_e164') }}">
                        <input type="hidden" id="whatsapp_country" name="whatsapp_country"
                            value="{{ old('whatsapp_country') }}">
                        <input type="hidden" id="whatsapp_dialcode" name="whatsapp_dialcode"
                            value="{{ old('whatsapp_dialcode') }}">
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
<!-- ✅ Fin Modal Inscription -->

<!-- ✅ Modal Voir Commande -->
<div class="modal fade" id="voirCommandeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1c911e;">
                <h5 class="modal-title text-white"><i class="fa fa-shopping-cart me-2"></i> Votre commande</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="commande-client-info"></div>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr class="text-center">
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                            <th>Total</th>
                            <th>X</th>
                        </tr>
                    </thead>
                    <tbody id="commande-produits"></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total général</th>
                            <th id="total-general">0 GNF</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <div class="d-flex gap-3 align-items-center">
                    <i id="btn-vue-facture" class="fa fa-eye text-primary" style="cursor:pointer;"
                        title="Voir la facture"></i>
                    <i id="btn-download-pdf" class="fa fa-file-pdf text-danger" style="cursor:pointer;"
                        title="Télécharger en PDF"></i>
                    <i id="btn-print-facture" class="fa fa-print text-success" style="cursor:pointer;"
                        title="Imprimer la facture"></i>
                </div>
                <button type="button" class="btn btn-danger text-light" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.authUser = @json(auth()->check() ? auth()->user() : null);
</script>

<style>
    /* Panier: style similaire à #side-moi pour comportement cohérent */
    #side-cart {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100%;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        transition: right 0.4s ease;
        z-index: 10001;
        overflow-y: auto;
    }

    #side-cart.on {
        right: 0;
    }

    #side-cart .close-side {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 18px;
        color: #333;
        cursor: pointer;
    }

    #side-moi {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100%;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        transition: right 0.4s ease;
        z-index: 10000;
        overflow-y: auto;
    }

    #side-moi.on {
        right: 0;
    }

    #side-moi .close-side {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 18px;
        color: #333;
        cursor: pointer;
    }

    /* Overlay quand les panneaux sont ouverts */
    body.side-open {
        overflow: hidden;
    }

    .side-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: none;
    }

    .side-overlay.show {
        display: block;
    }

    /* Dark mode styles */
    body.dark-mode {
        background-color: #1a1a1a;
        color: #e0e0e0;
    }

    body.dark-mode .navbar,
    body.dark-mode .main-header,
    body.dark-mode .main-top {
        background-color: #2d2d2d !important;
        color: #e0e0e0;
    }

    body.dark-mode .nav-link,
    body.dark-mode .navbar-brand .brand-text {
        color: #e0e0e0 !important;
    }

    body.dark-mode .nav-link.active {
        color: #28a745 !important;
    }

    body.dark-mode .modal-content {
        background-color: #2d2d2d;
        color: #e0e0e0;
    }

    body.dark-mode .modal-header {
        border-bottom-color: #444;
    }

    body.dark-mode .form-control,
    body.dark-mode .form-select {
        background-color: #3a3a3a;
        color: #e0e0e0;
        border-color: #555;
    }

    body.dark-mode .form-control::placeholder {
        color: #999;
    }

    body.dark-mode .form-control:focus,
    body.dark-mode .form-select:focus {
        background-color: #3a3a3a;
        color: #e0e0e0;
        border-color: #28a745;
    }

    body.dark-mode .side {
        background-color: #2d2d2d;
        color: #e0e0e0;
    }

    body.dark-mode .alert {
        background-color: #3a3a3a;
        color: #e0e0e0;
        border-color: #555;
    }

    body.dark-mode .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }

    body.dark-mode .btn-outline-success:hover {
        background-color: #28a745;
        color: white;
    }

    body.dark-mode .table {
        color: #e0e0e0;
    }

    body.dark-mode .table thead {
        background-color: #3a3a3a;
        color: #e0e0e0;
    }

    body.dark-mode .table tbody tr {
        border-color: #444;
    }

    body.dark-mode .info-item {
        background-color: rgba(100, 100, 100, 0.3);
        color: #e0e0e0;
    }
</style>

<script>
    // Appliquer le thème au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('appTheme') || 'light';
        applyTheme(savedTheme);
        updateThemeButton();
    });

    // Fonction pour appliquer le thème
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            localStorage.setItem('appTheme', 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('appTheme', 'light');
        }
        updateThemeButton();
    }

    // Mettre à jour l'icône et le label pour tous les boutons de toggle thème
    function updateThemeButton() {
        const buttons = Array.from(document.querySelectorAll('.btn-toggle-theme'));
        // also include legacy id if present
        const legacy = document.getElementById('btnToggleTheme');
        if (legacy && !buttons.includes(legacy)) buttons.push(legacy);

        const isDark = document.body.classList.contains('dark-mode');
        buttons.forEach(btn => {
            const icon = btn.querySelector('.theme-icon') || btn.querySelector('i');
            const label = btn.querySelector('.theme-label') || btn.querySelector('#themeLabel');
            if (icon) {
                icon.classList.remove('fa-moon', 'fa-sun');
                icon.classList.add(isDark ? 'fa-sun' : 'fa-moon');
            }
            btn.title = isDark ? 'Mode clair' : 'Mode sombre';
            if (label) label.textContent = isDark ? 'Mode clair' : 'Mode sombre';
            btn.classList.toggle('btn-outline-warning', isDark);
            btn.classList.toggle('btn-outline-primary', !isDark);
        });
    }

    // Attacher le handler à tous les boutons possibles
    document.querySelectorAll('.btn-toggle-theme, #btnToggleTheme').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const isDark = document.body.classList.contains('dark-mode');
            applyTheme(isDark ? 'light' : 'dark');
        });
    });
</script>
</style>
