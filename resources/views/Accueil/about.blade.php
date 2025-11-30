@extends('base')
@section('title', 'About')
@section('content')
    <div class="all-title-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>À propos de nous! </h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('app_accueil') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">À propos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End All Title Box -->

    <!-- Start About Page  -->
    <div class="about-box-main">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="banner-frame"> <img class="img-fluid" src="images/logo.png" alt="" />
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="noo-sh-title-top"> <span>Mourima Market</span></h2>
                    <p>
                        Mourima Market est une plateforme créée pour rapprocher les agriculteurs locaux des consommateurs.
                        Nous facilitons l'accès à des produits agricoles frais, de saison et tracés, tout en garantissant
                        une
                        juste rémunération aux producteurs. Notre mission est simple : soutenir les producteurs locaux
                        et offrir aux ménages des produits sains, de qualité et accessibles.
                    </p>

                    <p>
                        Depuis nos débuts, nous avons développé une chaîne logistique pensée pour préserver la qualité des
                        produits : collecte à la source, contrôle qualité avant expédition et livraison rapide sur toute la
                        ville. Nous travaillons avec des coopératives et petits exploitants pour améliorer les pratiques
                        agricoles et garantir la traçabilité des aliments.
                    </p>

                    <p>
                        <strong>Ce que nous apportons :</strong>
                    <ul>
                        <li>Produits frais et locaux, sélectionnés par nos experts</li>
                        <li>Livraison rapide et adaptée à vos besoins</li>
                        <li>Soutien et formation aux producteurs locaux</li>
                        <li>Transparence et traçabilité des lots</li>
                    </ul>
                    </p>

                    <p class="mt-3">
                        <a class="btn hvr-hover" href="{{ route('produits.allproduit') }}">Découvrir nos produits</a>
                        <a class="btn btn-outline-success ms-2" href="{{ route('app_contact') }}">Nous contacter</a>
                    </p>
                </div>
            </div>
            <div class="row my-5">
                <div class="col-sm-6 col-lg-4">
                    <div class="service-block-inner">
                        <h3>Qualité</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. </p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="service-block-inner">
                        <h3>Respect des engagements</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. </p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="service-block-inner">
                        <h3>Satisfaction client</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. </p>
                    </div>
                </div>
            </div>
            <div class="row my-4">
                <div class="col-12">
                    <h2 class="noo-sh-title">Nos employés</h2>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="hover-team">
                        <div class="our-team"> <img src="images/img-2.jpg" alt="" />
                            <div class="team-content">
                                <h3 class="title">Kahn</h3> <span class="post">Responsable IT</span>
                            </div>
                            <ul class="social">
                                <li>
                                    <a href="#" class="fab fa-facebook"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-twitter"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-google-plus"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-youtube"></a>
                                </li>
                            </ul>
                            <div class="icon"> <i class="fa fa-plus" aria-hidden="true"></i> </div>
                        </div>
                        <div class="team-description">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent urna diam, maximus ut
                                ullamcorper quis, placerat id eros. Duis semper justo sed condimentum rutrum. Nunc tristique
                                purus turpis. Maecenas vulputate. </p>
                        </div>
                        <hr class="my-0">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="hover-team">
                        <div class="our-team"> <img src="images/img-2.jpg" alt="" />
                            <div class="team-content">
                                <h3 class="title">Binta</h3> <span class="post">Responsable de la production</span>
                            </div>
                            <ul class="social">
                                <li>
                                    <a href="#" class="fab fa-facebook"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-twitter"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-google-plus"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-youtube"></a>
                                </li>
                            </ul>
                            <div class="icon"> <i class="fa fa-plus" aria-hidden="true"></i> </div>
                        </div>
                        <div class="team-description">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent urna diam, maximus ut
                                ullamcorper quis, placerat id eros. Duis semper justo sed condimentum rutrum. Nunc tristique
                                purus turpis. Maecenas vulputate. </p>
                        </div>
                        <hr class="my-0">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="hover-team">
                        <div class="our-team"> <img src="images/img-2.jpg" alt="" />
                            <div class="team-content">
                                <h3 class="title">Sangaré</h3> <span class="post">Responsable IT Adjoin</span>
                            </div>
                            <ul class="social">
                                <li>
                                    <a href="#" class="fab fa-facebook"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-twitter"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-google-plus"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-youtube"></a>
                                </li>
                            </ul>
                            <div class="icon"> <i class="fa fa-plus" aria-hidden="true"></i> </div>
                        </div>
                        <div class="team-description">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent urna diam, maximus ut
                                ullamcorper quis, placerat id eros. Duis semper justo sed condimentum rutrum. Nunc tristique
                                purus turpis. Maecenas vulputate. </p>
                        </div>
                        <hr class="my-0">
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="hover-team">
                        <div class="our-team"> <img src="images/img-2.jpg" alt="" />
                            <div class="team-content">
                                <h3 class="title">Mariam</h3> <span class="post">R.A.F.A</span>
                            </div>
                            <ul class="social">
                                <li>
                                    <a href="#" class="fab fa-facebook"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-twitter"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-google-plus"></a>
                                </li>
                                <li>
                                    <a href="#" class="fab fa-youtube"></a>
                                </li>
                            </ul>
                            <div class="icon"> <i class="fa fa-plus" aria-hidden="true"></i> </div>
                        </div>
                        <div class="team-description">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent urna diam, maximus ut
                                ullamcorper quis, placerat id eros. Duis semper justo sed condimentum rutrum. Nunc tristique
                                purus turpis. Maecenas vulputate. </p>
                        </div>
                        <hr class="my-0">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
