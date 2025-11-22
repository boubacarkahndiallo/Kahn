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
            /* Pas de bordure visible */
            border: none !important;
            color: #fff !important;
            transition: all 0.25s cubic-bezier(.2, .8, .2, 1) !important;
            /* Ombre plus marquée et contraste pour la visibilité */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18), 0 6px 18px rgba(28, 145, 30, 0.22) !important;
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

        /* Rendre le bouton visible et utilisable sur petits écrans */
        @media (max-width: 576px) {
            .btn-cta-commande {
                display: inline-flex !important;
                width: 100% !important;
                justify-content: center !important;
                padding: 12px 18px !important;
                font-size: 15px !important;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18), 0 4px 12px rgba(28, 145, 30, 0.16) !important;
                white-space: normal !important;
                text-align: center !important;
                z-index: 9999 !important;
            }

            .slide-in-left .btn-cta-commande,
            .delay-3 .btn-cta-commande {
                display: inline-flex !important;
            }
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

        /* Forcer visibilité sur petits écrans : bouton et texte du slider */
        @media (max-width: 576px) {
            .cover-slides {
                height: 50vh !important;
                min-height: 280px !important;
                max-height: 60vh !important;
                overflow: hidden !important;
            }

            .cover-slides .slides-container {
                height: 100% !important;
                width: 100% !important;
                list-style: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .cover-slides .slides-container li {
                height: 100% !important;
                width: 100% !important;
                display: block !important;
                position: relative !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                background-size: cover !important;
                background-position: center !important;
            }

            .cover-slides .slides-container li img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: center !important;
                display: block !important;
            }

            .cover-slides .slides-container li .container {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                z-index: 10 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .cover-slides .container {
                z-index: 9999 !important;
            }

            .cover-slides h1,
            .cover-slides p,
            .slide-in-left,
            .delay-1,
            .delay-2,
            .delay-3 {
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
                visibility: visible !important;
                z-index: 99999 !important;
            }

            .slides-container li .overlay-background {
                z-index: 1 !important;
                pointer-events: none !important;
            }

            .btn-cta-commande {
                display: inline-flex !important;
                width: 100% !important;
                z-index: 100000 !important;
            }
        }
    </style>

    <!-- Start Slider -->
    <div id="slides-shop" class="cover-slides">
        <ul class="slides-container">
            @foreach (['banner-01.jpg', 'banner-02.jpg', 'banner-03.jpg'] as $index => $banner)
                <li data-slide="{{ $index + 1 }}">
                    <img src="{{ asset('images/' . $banner) }}" alt="Slide {{ $index + 1 }}"
                        data-banner="{{ $banner }}">
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
                                            Cliquez pour commander
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
            // Fix superslides cloning issue - ensure cloned images have correct src
            $(document).ready(function() {
                // Store original image sources before superslides modifies DOM
                const originalImages = {};
                $('.slides-container li').each(function(index) {
                    const img = $(this).find('img');
                    originalImages[index] = img.attr('src');
                });

                // After superslides initializes, fix any cloned images
                setTimeout(function() {
                    $('.slides-container li img').each(function(index) {
                        const $img = $(this);
                        const parentIndex = $img.closest('li').index();
                        const correctSrc = originalImages[parentIndex % Object.keys(originalImages)
                            .length];
                        if (correctSrc && !$img.attr('src').includes(correctSrc.split('/').pop())) {
                            $img.attr('src', correctSrc);
                        }
                    });
                }, 100);
            });

            // Force slider recalculation after images load to fix mobile display
            $(window).on('load', function() {
                setTimeout(function() {
                    try {
                        // Trigger resize to recalculate slider dimensions
                        $(window).trigger('resize');
                        if ($.fn.superslides) {
                            $('#slides-shop').superslides('animate');
                        }
                    } catch (e) {
                        console.log('Slider recalc:', e);
                    }
                }, 300);
            });

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
