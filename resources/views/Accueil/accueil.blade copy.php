@extends('base')
@section('title', 'Accueil')
@section('content')
    <!-- CSS pour le bouton CTA personnalisé -->
    <style>
        .btn-cta-commande {
            position: relative;
            padding: 18px 35px !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            background: linear-gradient(135deg, #1c911e 0%, #14680f 100%) !important;
            border: 3px solid #ffffff !important;
            color: #fff !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 8px 24px rgba(28, 145, 30, 0.35) !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 10px !important;
        }

        .btn-cta-commande:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 12px 32px rgba(28, 145, 30, 0.5) !important;
            color: #fff !important;
            text-decoration: none !important;
        }

        .btn-cta-commande:active {
            transform: translateY(-1px) !important;
        }

        @keyframes pulse-cta {

            0%,
            100% {
                box-shadow: 0 8px 24px rgba(28, 145, 30, 0.35), 0 0 0 0 rgba(28, 145, 30, 0.2);
            }

            50% {
                box-shadow: 0 8px 24px rgba(28, 145, 30, 0.35), 0 0 0 15px rgba(28, 145, 30, 0);
            }
        }

        .btn-cta-commande.pulsing {
            animation: pulse-cta 2.5s infinite;
        }

        .btn-cta-commande i {
            font-size: 22px;
            animation: bounce 1s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }
    </style>

    <!-- Start Slider -->
    <div id="slides-shop" class="cover-slides">
        <ul class="slides-container">
            @foreach (['banner-01.jpg', 'banner-02.jpg', 'banner-03.jpg'] as $banner)
                <li>
                    <img src="{{ asset('images/' . $banner) }}" alt="">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <h1 class="m-b-20 slide-in-left delay-1 text-uppercase">
                                        <strong>Bienvenue</strong>
                                    </h1>
                                    <h1 class="m-b-20 slide-in-left delay-2">
                                        <strong>
                                            <span class="chez">Chez</span>
                                            <span class="mourima">Mourima Market</span>
                                        </strong>
                                    </h1>
                                    <p class="slide-in-left delay-3">
                                        <a class="btn btn-cta-commande pulsing hvr-hover"
                                            href="{{ route('produits.allproduit') }}">
                                            <i class="fas fa-shopping-cart"></i>
                                            Cliquez ici pour commander
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="slides-navigation">
            <a href="#" class="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
            <a href="#" class="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
        </div>
    </div>
    <!-- End Slider -->

    <!-- Start Custom Sections (Categories / Avantages / Services / CTA) -->

    {{-- styles moved to public/css/style2.css (linked in base) --}}

    <!-- Start Categories -->
    <section class="categories-shop py-5">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-4 reveal" data-delay="0">
                    <a href="{{ route('produits.allproduit') }}?category=legumes" class="category-card d-block">
                        <img loading="lazy" src="{{ asset('images/categories_img_01.jpg') }}" alt="Légumes">
                        <div class="category-overlay">
                            <h4>Légumes</h4>
                            <p>Fraîcheur du marché</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 reveal" data-delay="100">
                    <a href="{{ route('produits.allproduit') }}?category=fruits" class="category-card d-block">
                        <img loading="lazy" src="{{ asset('images/categories_img_02.jpg') }}" alt="Fruits">
                        <div class="category-overlay">
                            <h4>Fruits</h4>
                            <p>Sucrés et juteux</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 reveal" data-delay="200">
                    <a href="{{ route('produits.allproduit') }}?category=viande" class="category-card d-block">
                        <img loading="lazy" src="{{ asset('images/categories_img_03.jpg') }}" alt="Viande et poisson">
                        <div class="category-overlay">
                            <h4>Viande & Poisson</h4>
                            <p>Qualité contrôlée</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- À propos / Avantages -->
    <section class="advantages bg-light text-center">
        <div class="container">
            <h2 class="fw-bold">Pourquoi nous choisir ?</h2>
            <div class="section-hr"></div>
            <p class="lead">Nous soutenons les producteurs locaux et garantissons des produits frais, sains et
                accessibles.</p>
            <div class="row mt-5">
                <div class="col-md-4 reveal" data-delay="0">
                    <div class="adv-icon"><i class="fas fa-leaf"></i></div>
                    <h5 class="mt-3">Produits bio</h5>
                    <p class="text-muted">Des fruits et légumes 100% naturels sans produits chimiques.</p>
                </div>
                <div class="col-md-4 reveal" data-delay="100">
                    <div class="adv-icon"><i class="fas fa-truck"></i></div>
                    <h5 class="mt-3">Livraison rapide</h5>
                    <p class="text-muted">Commandez et recevez vos produits en un temps record.</p>
                </div>
                <div class="col-md-4 reveal" data-delay="200">
                    <div class="adv-icon"><i class="fas fa-handshake"></i></div>
                    <h5 class="mt-3">Soutien local</h5>
                    <p class="text-muted">Chaque achat aide directement les agriculteurs de la région.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Start Services (modern cards) -->
    <section class="services py-5">
        <div class="container">
            <div class="title-all text-center mb-4">
                <h1>Nos services</h1>
                <div class="section-hr"></div>
                <p class="text-muted">Avec Mourima Enterprise, profitez d’une expérience simple et fiable.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4 reveal" data-delay="0">
                    <div class="service-card">
                        <div class="service-img mb-3">
                            <img loading="lazy" src="{{ asset('images/un-homme-africain-recolte-des-legumes.jpg') }}"
                                alt="Livraison">
                        </div>
                        <h5>Livraison rapide</h5>
                        <p class="text-muted">Nous assurons la livraison directe de vos produits agricoles frais à domicile,
                            au marché ou à votre restaurant.</p>
                        <div class="mt-auto text-end">
                            <a href="#" class="text-success">En savoir plus &raquo;</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 reveal" data-delay="100">
                    <div class="service-card">
                        <div class="service-img mb-3">
                            <img loading="lazy" src="{{ asset('images/IMG-20250219-WA0019.jpg') }}" alt="Qualité">
                        </div>
                        <h5>Produits de qualité</h5>
                        <p class="text-muted">Nous sélectionnons rigoureusement chaque produit pour assurer fraîcheur et
                            qualité supérieure.</p>
                        <div class="mt-auto text-end">
                            <a href="#" class="text-success">En savoir plus &raquo;</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 reveal" data-delay="200">
                    <div class="service-card">
                        <div class="service-img mb-3">
                            <img loading="lazy" src="{{ asset('images/cover-octobre.jpg') }}" alt="Support producteurs">
                        </div>
                        <h5>Support aux producteurs</h5>
                        <p class="text-muted">Nous travaillons directement avec les producteurs pour garantir des prix
                            justes et une qualité optimale.</p>
                        <div class="mt-auto text-end">
                            <a href="#" class="text-success">En savoir plus &raquo;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA final with background image and green overlay -->
    <section class="cta-final reveal" data-delay="100"
        style="background-image: url('{{ asset('images/banner-06.jpg') }}'); position:relative;">
        <div class="cta-overlay text-center">
            <div class="container">
                <h2 class="fw-bold">Commandez maintenant et profitez de la fraîcheur</h2>
                <p class="lead">Promotion spéciale pour les premières commandes.</p>
                <a href="{{ route('produits.allproduit') }}" class="btn-cta btn-lg">Découvrir</a>
            </div>
        </div>
    </section>

    <!-- End Custom Sections -->

    @push('scripts')
        <script>
            // Small IntersectionObserver to add .visible to .reveal elements
            document.addEventListener('DOMContentLoaded', function() {
                const revealElements = document.querySelectorAll('.reveal');
                if ('IntersectionObserver' in window) {
                    const io = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const el = entry.target;
                                const delay = el.getAttribute('data-delay') || 0;
                                setTimeout(() => el.classList.add('visible'), parseInt(delay));
                                io.unobserve(el);
                            }
                        });
                    }, {
                        threshold: 0.12
                    });

                    revealElements.forEach(el => io.observe(el));
                } else {
                    // Fallback: show all
                    revealElements.forEach(el => el.classList.add('visible'));
                }
            });
        </script>
    @endpush

    <!-- Modal Ajouter au Panier -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Passer une commande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="modal-produit-image" src="" class="img-fluid rounded" alt="Produit">
                        </div>
                        <div class="col-md-8">
                            <h4 id="modal-produit-nom"></h4>
                            <p>Prix unitaire : <span id="modal-produit-prix"></span> GNF</p>

                            <div class="mb-3">
                                <label for="modal-quantite" class="form-label">Quantité</label>
                                <input type="number" id="modal-quantite" class="form-control" min="1"
                                    value="1">
                            </div>

                            <h5>Total : <span id="modal-total"></span> GNF</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-success" id="btn-valider-commande">Valider la commande</button>
                </div>
            </div>
        </div>
    </div>

@endsection
