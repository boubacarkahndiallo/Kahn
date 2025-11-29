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
                        <li><a href="https://wa.me/224623248567"><i class="fab fa-whatsapp"></i> +224 623 24 85 67</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="text-slid-box">
                    <div id="offer-box" class="carouselTicker">
                        <ul class="offer-box">
                            <li><i class="fab fa-opencart"></i> Des produits agricoles 100% frais</li>
                            <li><i class="fab fa-opencart"></i> Variété de produits pour répondre à vos besoins</li>
                            <li><i class="fab fa-opencart"></i> Commandez et faites-vous livrer rapidement</li>
                            <li><i class="fab fa-opencart"></i> Soutenons les producteurs locaux</li>
                            <li><i class="fab fa-opencart"></i> L’agriculture au service de votre assiette</li>
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
        /* --- NAVBAR MOBILE STYLE APP --- */
        @media (max-width: 991px) {
            nav.navbar {
                background: linear-gradient(135deg, #1c911e 0%, #14680f 100%) !important;
                border-radius: 0 0 22px 22px !important;
                box-shadow: 0 6px 24px rgba(28, 145, 30, 0.18), 0 2px 8px rgba(28, 145, 30, 0.22) !important;
                position: fixed !important;
                top: 0;
                left: 0;
                right: 0;
                z-index: 2000;
                width: 100vw;
                min-height: 62px;
                padding: 0 0.5rem !important;
                margin: 0 auto !important;
                transition: box-shadow 0.3s;
            }

            nav.navbar .container {
                border-radius: 0 0 22px 22px;
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            nav.navbar .navbar-header {
                margin-bottom: 0.2rem;
            }

            nav.navbar .navbar-brand img.logo {
                border-radius: 10px !important;
                box-shadow: 0 1px 6px rgba(0, 0, 0, 0.08);
                background: #fff;
                padding: 0.5rem 0 !important;
                margin-top: 38px !important;
                /* Augmente l'espace au-dessus du logo sur mobile */
            }

            nav.navbar .navbar-brand .brand-text {
                font-size: 1.15rem;
                font-weight: 900;
                color: #fff !important;
                letter-spacing: 0.5px;
                text-shadow: 0 2px 8px rgba(28, 145, 30, 0.12);
            }

            .navbar-toggler {
                background: #fff !important;
                border-radius: 50% !important;
                width: 34px;
                height: 34px;
                box-shadow: 0 2px 6px rgba(28, 145, 30, 0.08);
                color: #1c911e !important;
                font-size: 1.05rem !important;
                border: none !important;
                padding: 0;
            }

            #navbar-menu {
                background: rgba(255, 255, 255, 0.98) !important;
                border-radius: 18px !important;
                margin-top: 0.5rem;
                box-shadow: 0 2px 12px rgba(28, 145, 30, 0.10);
                padding: 0.5rem 0.5rem 0.7rem 0.5rem !important;
            }

            #navbar-menu .navbar-nav {
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.2rem !important;
            }

            #navbar-menu .nav-link {
                color: #1c911e !important;
                font-size: 1.08rem !important;
                font-weight: 700;
                border-radius: 12px;
                padding: 0.5rem 1.1rem !important;
                margin: 0.1rem 0;
                background: #f8f9fa !important;
                box-shadow: 0 1px 4px rgba(28, 145, 30, 0.07);
                transition: background 0.2s, color 0.2s;
            }

            #navbar-menu .nav-link.active,
            #navbar-menu .nav-link:active,
            #navbar-menu .nav-link:focus {
                background: #1c911e !important;
                color: #fff !important;
            }

            .attr-nav ul {
                display: flex !important;
                flex-direction: row !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 0.7rem !important;
                margin: 0.2rem 0 0.1rem 0 !important;
                padding: 0 !important;
            }

            .attr-nav ul li {
                background: #fff;
                border-radius: 50%;
                box-shadow: 0 1px 6px rgba(28, 145, 30, 0.08);
                width: 36px;
                height: 36px;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin: 0 2px;
                position: relative;
                padding: 0 !important;
            }

            /* Masquer l'icône notifications du header sur mobile
               (nous utiliserons l'icône footer à la place) */
            .attr-nav ul li.nav-notification {
                display: none !important;
                background: transparent !important;
                box-shadow: none !important;
                width: auto !important;
                height: auto !important;
            }

            .attr-nav ul li a {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 100% !important;
                height: 100% !important;
            }

            .attr-nav ul li i {
                font-size: 1.05rem !important;
                color: #1c911e !important;
                margin: 0 !important;
            }

            .attr-nav ul li .badge {
                position: absolute;
                top: -4px;
                right: -4px;
                background: #e74c3c;
                color: #fff;
                font-size: 0.75rem;
                border-radius: 50%;
                min-width: 18px;
                min-height: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 1px 4px rgba(231, 76, 60, 0.12);
            }

            /* Afficher le bouton login sur mobile quand pas connecté */
            .login-btn-mobile {
                display: flex !important;
            }

            /* Effet flottant sur mobile */
            nav.navbar {
                box-shadow: 0 8px 32px rgba(28, 145, 30, 0.18), 0 2px 8px rgba(28, 145, 30, 0.22) !important;
            }
        }

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

            /* Navbar header à gauche sur mobile */
            nav.navbar .navbar-header {
                position: relative;
                width: 100%;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                margin-bottom: 1rem;
                gap: 0.5rem;
            }

            /* Marque/logo aligné à gauche */
            nav.navbar .navbar-brand {
                margin: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
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
                padding: 5 !important;
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
    <!-- Footer navbar mobile -->
    <nav id="mobile-footer-navbar" class="d-lg-none">
        <ul>
            <li>
                <a href="{{ route('app_accueil') }}" aria-label="Accueil">
                    <i class="fa fa-home"></i>
                    <span>Accueil</span>
                </a>
            </li>
            @auth
                <li>
                    <a href="{{ route('app_dashboard') }}" aria-label="Dashboard">
                        <i class="fa fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            @endauth

            <li class="footer-prd-dropdown" style="position:relative;">
                <a href="#" aria-label="Produits" id="footer-prd-toggle" aria-label="Produits">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Produits</span>
                </a>
                <ul id="footer-prd-menu" class="footer-dropdown-menu"
                    style="display:none; position:absolute; bottom:40px; left:50%; transform:translateX(-50%); background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(28,145,30,0.12); min-width:120px; z-index:4000;">
                    @if (auth()->check() && !in_array(auth()->user()->role, ['client', 'fournisseur']))
                        <li><a href="{{ route('produits.index') }}">Ajout produit</a></li>
                    @endif
                    <li><a href="{{ route('produits.allproduit') }}">Nos produits</a></li>
                </ul>
            </li>

            </li>
            @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin']))
                <li class="footer-user-dropdown" style="position:relative;">
                    <a href="#" id="footer-user-toggle" aria-label="Utilisateurs">
                        <i class="fa fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <ul id="footer-user-menu" class="footer-dropdown-menu"
                        style="display:none; position:absolute; bottom:40px; left:50%; transform:translateX(-50%); background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(28,145,30,0.12); min-width:120px; z-index:4000;">
                        <li><a href="{{ route('admin.users.index') }}">Utilisateur</a></li>
                        <li><a href="{{ route('clients.index') }}">Client</a></li>
                        <li><a href="#">Fournisseur</a></li>
                    </ul>
                </li>
            @endif
            @if (auth()->check() && !in_array(auth()->user()->role, ['client', 'fournisseur']))
                <li class="footer-cmd-dropdown" style="position:relative;">
                    <a href="#" id="footer-cmd-toggle" aria-label="Commandes">
                        <i class="fa fa-list"></i>
                        <span>Commandes</span>
                    </a>
                    <ul id="footer-cmd-menu" class="footer-dropdown-menu"
                        style="display:none; position:absolute; bottom:40px; left:50%; transform:translateX(-50%); background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(28,145,30,0.12); min-width:120px; z-index:4000;">
                        <li><a href="{{ route('commandes.index') }}">Commandes</a></li>
                        <li><a href="{{ route('livraisons.index') }}">Livraisons</a></li>
                    </ul>
                </li>
            @endif
            <li>
                <a href="{{ route('app_contact') }}" aria-label="Contact">
                    <i class="fa fa-envelope"></i>
                    <span>Contact</span>
                </a>
            </li>
            <li>
                <a href="#" id="footer-moi" aria-label="Profil">
                    <i class="fa fa-user"></i>
                    <span>Moi</span>
                </a>
            </li>
            <li class="footer-notif-dropdown" style="position:relative;">
                <a href="#" id="footer-notif-toggle" aria-label="Notifications" style="position:relative;">
                    <i class="fa fa-bell"></i>
                    <span id="footer-notif-count" class="badge bg-danger"
                        style="position:absolute;top:-6px;right:-6px;display:none;">0</span>
                    <span>Notifications</span>
                </a>
                <ul id="footer-notif-menu" class="footer-dropdown-menu"
                    style="display:none; position:absolute; bottom:40px; left:6px; transform:none; background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(28,145,30,0.12); min-width:200px; z-index:4000;">
                    <li><a href="#">Voir les notifications</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <style>
        .footer-dropdown-menu.show {
            display: block !important;
        }

        .footer-dropdown-menu {
            display: none;
        }

        /* Masquer l'icône "Moi" dans la navbar principale sur mobile (ne pas toucher au footer ni au desktop) */
        @media (max-width: 991px) {
            .side-moi {
                display: none !important;
            }

            /* Règle plus spécifique pour surcharger d'autres styles */
            nav.navbar .attr-nav ul li.side-moi,
            nav.navbar .attr-nav li.side-moi {
                display: none !important;
            }
        }

        #mobile-footer-navbar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            background: linear-gradient(135deg, #fff 80%, #e8f5e9 100%);
            box-shadow: 0 -2px 16px rgba(28, 145, 30, 0.10);
            z-index: 3000;
            border-radius: 18px 18px 0 0;
            padding: 0.2rem 0 0.1rem 0;
        }

        #mobile-footer-navbar ul {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        #mobile-footer-navbar li {
            flex: 1 1 0;
            text-align: center;
        }

        #mobile-footer-navbar a {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #1c911e;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            padding: 0.3rem 0 0.1rem 0;
            transition: color 0.2s;
        }

        #mobile-footer-navbar a:active,
        #mobile-footer-navbar a:focus {
            color: #14680f;
        }

        #mobile-footer-navbar i {
            font-size: 1.35rem;
            margin-bottom: 2px;
        }

        #mobile-footer-navbar span {
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Réduire la taille des boutons prev/next du slider sur mobile */
        @media (max-width: 991px) {

            .slides-navigation a.next,
            .slides-navigation a.prev {
                width: 34px;
                height: 34px;
                line-height: 34px;
                font-size: 1rem;
                border-radius: 50%;
                background: #1c911e;
                /* fond vert */
                color: #fff !important;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .slides-navigation a.next i,
            .slides-navigation a.prev i {
                font-size: 0.9rem !important;
            }

            .slides-navigation a.next:hover,
            .slides-navigation a.prev:hover {
                background: #14680f;
                /* teinte plus foncée au hover */
                color: #fff !important;
            }
        }

        @media (min-width: 992px) {
            #mobile-footer-navbar {
                display: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ouvre le panneau Moi depuis le footer
            const btnMoi = document.getElementById('footer-moi');
            if (btnMoi) {
                btnMoi.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sideMoi = document.getElementById('side-moi');
                    if (sideMoi) sideMoi.classList.add('on');
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown utilisateur mobile
            const userToggle = document.getElementById('footer-user-toggle');
            const userMenu = document.getElementById('footer-user-menu');
            if (userToggle && userMenu) {
                userToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    userMenu.classList.toggle('show');
                });
                document.addEventListener('click', function(e) {
                    if (!userToggle.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.remove('show');
                    }
                });
            }
        });

        // Initialiser autoplay du slider sur mobile seulement
        (function() {
            function initMobileAutoplay() {
                try {
                    var isMobile = window.matchMedia && window.matchMedia('(max-width: 991px)').matches;
                    if (isMobile && window.jQuery && jQuery.fn.superslides) {
                        var $slides = jQuery('#slides-shop');
                        // évitez la ré-initialisation si déjà initialisé
                        if (!$slides.data('superslides')) {
                            $slides.superslides({
                                play: 4000,
                                animation: 'fade',
                                pagination: false
                            });
                        } else {
                            // si déjà initialisé, déclencher play si possible
                            try {
                                $slides.superslides('start');
                            } catch (e) {}
                        }
                    }
                } catch (e) {
                    console && console.warn && console.warn('Init mobile autoplay failed', e);
                }
            }

            // Init au chargement complet (images chargées)
            window.addEventListener('load', function() {
                setTimeout(initMobileAutoplay, 250);
            });
            // Et aussi à resize pour gérer changement d'orientation
            window.addEventListener('resize', function() {
                setTimeout(initMobileAutoplay, 300);
            });
        })();
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown Commandes mobile
            const userToggle = document.getElementById('footer-cmd-toggle');
            const userMenu = document.getElementById('footer-cmd-menu');
            if (userToggle && userMenu) {
                userToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    userMenu.classList.toggle('show');
                });
                document.addEventListener('click', function(e) {
                    if (!userToggle.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.remove('show');
                    }
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown Notifications mobile (ouvre depuis la gauche et seulement au clic)
            const notifToggle = document.getElementById('footer-notif-toggle');
            const notifMenu = document.getElementById('footer-notif-menu');
            if (notifToggle) {
                notifToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Open side-notifications panel instead of small dropdown on mobile
                    const sideCart = document.getElementById('side-cart');
                    const sideMoi = document.getElementById('side-moi');
                    const sideNotifications = document.getElementById('side-notifications');
                    const sideOverlay = document.getElementById('side-overlay');
                    if (sideCart) sideCart.classList.remove('on');
                    if (sideMoi) sideMoi.classList.remove('on');
                    if (sideNotifications) sideNotifications.classList.add('on');
                    if (sideOverlay) sideOverlay.classList.add('show');
                    document.body.classList.add('side-open');
                }, true);
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown Commandes mobile
            const userToggle = document.getElementById('footer-prd-toggle');
            const userMenu = document.getElementById('footer-prd-menu');
            if (userToggle && userMenu) {
                userToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    userMenu.classList.toggle('show');
                });
                document.addEventListener('click', function(e) {
                    if (!userToggle.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
    <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-default bootsnav">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('app_accueil') }}">
                    <img src="{{ asset('images/logo1.png') }}"
                        srcset="{{ asset('images/logo1.png') }} 1x, {{ asset('images/logo1.png') }} 2x"
                        class="logo" alt="Logo">
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
                    <!-- Notifications: cloche (ouvre panneau latéral comme Moi / Panier) -->
                    <li class="nav-item nav-notification">
                        <a href="#" class="nav-link" id="nav-notifications" aria-expanded="false"
                            style="position:relative;">
                            <i class="fa fa-bell"></i>
                            <span id="notif-count" class="badge bg-danger"
                                style="position:absolute;top:-6px;right:-6px;display:none;">0</span>
                        </a>
                    </li>
                    @auth
                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                            <li class="side-notifications" style="position: relative;">
                                <a href="{{ route('notifications.index') }}">
                                    <i class="fa fa-bell"></i>
                                    <span class="badge bg-danger" id="notification-badge-navbar"
                                        style="display: none;">0</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                    @guest
                        <li class="login-btn-mobile" style="display: none;">
                            <a href="{{ route('login') }}" title="Se connecter">
                                <i class="fas fa-sign-in-alt"></i>
                            </a>
                        </li>
                    @endguest
                    <li class="side-moi d-none d-lg-flex">
                        <a href="#" class="d-none d-lg-flex">
                            <i class="fa fa-user" id="btn-moi"></i>
                        </a>
                    </li>
                    <li class="side-menu">
                        <a href="#" id="cart-link">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="badge bg-success" id="cart-count">0</span>
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

        <!-- Panneau Notifications (ouvre depuis la droite comme Moi / Panier) -->
        <div class="side" id="side-notifications">
            <a href="#" class="close-side"><i class="fa fa-times"></i></a>
            <div class="cart-box">
                <ul class="cart-list list-unstyled p-3">
                    <li class="mb-2">
                        <h5 class="text-success mb-0"><i class="fa fa-bell me-2"></i> Notifications</h5>
                    </li>
                    <li>
                        <hr>
                    </li>
                    <li id="notif-list" style="max-height:70vh; overflow:auto;"></li>
                    <li>
                        <hr>
                    </li>
                    <li class="text-center"><a href="#" id="mark-all-read">Marquer tout lu</a></li>
                </ul>
            </div>
        </div>

        <!-- Toast container pour notifications -->
        <div id="toast-container" class="position-fixed bottom-0 end-0 p-3"
            style="z-index:10800; pointer-events: none;"></div>

        <!-- Overlay global pour fermer les panneaux latéraux -->
        <div class="side-overlay" id="side-overlay"></div>

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

                // Gestionnaire pour le panneau "Notifications"
                const btnNotifications = document.getElementById('nav-notifications');
                const sideNotifications = document.getElementById('side-notifications');
                const closeSideNotifications = sideNotifications ? sideNotifications.querySelector('.close-side') :
                    null;

                if (btnNotifications) {
                    btnNotifications.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debug('click notifications');
                        // Fermer les autres panneaux
                        if (sideCart) sideCart.classList.remove('on');
                        if (sideMoi) sideMoi.classList.remove('on');
                        if (sideNotifications) sideNotifications.classList.add('on');
                        // show overlay and lock body scroll
                        document.getElementById('side-overlay').classList.add('show');
                        document.body.classList.add('side-open');
                    }, true);
                } else {
                    debug('btnNotifications non trouvé');
                }

                if (closeSideNotifications) {
                    closeSideNotifications.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debug('close sideNotifications');
                        if (sideNotifications) sideNotifications.classList.remove('on');
                        document.getElementById('side-overlay').classList.remove('show');
                        document.body.classList.remove('side-open');
                    });
                }

                // Gestionnaire pour le panneau "Panier"

                if (btnCart) {
                    btnCart.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debug('click btnCart', btnCart);
                        // Fermer les autres panneaux
                        if (sideMoi) sideMoi.classList.remove('on');
                        if (sideNotifications) sideNotifications.classList.remove('on');
                        if (sideCart) sideCart.classList.add('on');
                        // show overlay and lock body scroll
                        document.getElementById('side-overlay').classList.add('show');
                        document.body.classList.add('side-open');
                    }, true);
                } else {
                    debug('btnCart non trouvé');
                }

                if (closeCart) {
                    closeCart.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        debug('close sideCart');
                        if (sideCart) sideCart.classList.remove('on');
                        document.getElementById('side-overlay').classList.remove('show');
                        document.body.classList.remove('side-open');
                    });
                }

                // Click overlay to close all panels
                const sideOverlay = document.getElementById('side-overlay');
                if (sideOverlay) {
                    sideOverlay.addEventListener('click', function(e) {
                        e.preventDefault();
                        debug('overlay click - close all panels');
                        if (sideCart) sideCart.classList.remove('on');
                        if (sideMoi) sideMoi.classList.remove('on');
                        if (sideNotifications) sideNotifications.classList.remove('on');
                        sideOverlay.classList.remove('show');
                        document.body.classList.remove('side-open');
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
                                const html = data.map(product => `
                                <div class="card mb-2">
                                    <div class="card-body d-flex">
                                        <div class="me-3" style="width: 100px; height: 100px;">
                                            <img src="${window.STORAGE_URL}/${product.image}" alt="${product.nom}"
                                                class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <h5 class="card-title">${product.nom}</h5>
                                            <p class="card-text">Prix: ${product.prix} GNF</p>
                                            <a href="{{ route('produits.allproduit') }}?product_id=${product.id}" class="btn btn-sm" style="background:#1c911e; color:white;">Commander</a>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
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
                                    inputmode="tel" autocomplete="tel" aria-describedby="telFeedback_navbar"
                                    value="{{ old('tel') }}">
                                <input type="hidden" name="tel_e164" id="tel_e164" value="{{ old('tel_e164') }}">
                                <input type="hidden" name="tel_country" id="tel_country"
                                    value="{{ old('tel_country') }}">
                                <input type="hidden" name="tel_dialcode" id="tel_dialcode"
                                    value="{{ old('tel_dialcode') }}">
                                <div class="invalid-feedback" id="telFeedback_navbar"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="whatsapp">WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text phone-flag" aria-hidden="false" role="button"
                                    tabindex="0" aria-label="Changer le pays"></span>
                                <input type="tel" class="form-control" name="whatsapp" id="whatsapp"
                                    inputmode="tel" autocomplete="tel" aria-describedby="whatsappFeedback_navbar"
                                    value="{{ old('whatsapp') }}">
                                <input type="hidden" name="whatsapp_e164" id="whatsapp_e164"
                                    value="{{ old('whatsapp_e164') }}">
                                <input type="hidden" name="whatsapp_country" id="whatsapp_country"
                                    value="{{ old('whatsapp_country') }}">
                                <input type="hidden" name="whatsapp_dialcode" id="whatsapp_dialcode"
                                    value="{{ old('whatsapp_dialcode') }}">
                                <div class="invalid-feedback" id="whatsappFeedback_navbar"></div>
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

    /* Panneau Notifications (même comportement que side-cart / side-moi) */
    #side-notifications {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100%;
        background: #fff;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
        transition: right 0.4s ease;
        z-index: 10002;
        overflow-y: auto;
    }

    #side-notifications.on {
        right: 0;
    }

    #side-notifications .close-side {
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

    /* Styling pour la cloche notifications */
    const style = document.createElement('style');
    style.textContent = `
        .side-notifications {
            position: relative !important;
        }

        .side-notifications a {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
        }

        .side-notifications i {
            font-size: 16px;
            color: #1c911e;
            transition: all 0.3s ease;
        }

        .side-notifications:hover i {
            transform: scale(1.1);
        }

        .side-notifications .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 22px;
            height: 22px;
            font-size: 11px;
            padding: 0;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
    `;
    document.head.appendChild(style);
</script>
</style>
