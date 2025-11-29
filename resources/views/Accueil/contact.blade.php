@extends('base')
@section('title', 'Contactez-nous')
@section('content')

    <!-- Start All Title Box -->
    <div class="all-title-box contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Contactez-nous</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('app_accueil') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Contact</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End All Title Box -->

    <!-- Start Contact Us  -->
    <div class="contact-box-main py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7 col-sm-12">
                    <div class="contact-form-right p-4 shadow-sm rounded bg-white">
                        <h3 class="mb-3">Nous joindre</h3>
                        <p class="text-muted">Avez-vous une question ou une demande ? Laissez-nous un message et nous vous
                            répondrons rapidement.</p>

                        <div id="contactAlert" class="alert d-none" role="alert"></div>

                        <form id="contactForm" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Prénom et nom</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Boubacar Kahn DIALLO" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <div class="input-group">
                                        <span class="input-group-text phone-flag" aria-hidden="false" role="button"
                                            tabindex="0" aria-label="Changer le pays"></span>
                                        <input type="tel" class="form-control" id="telephone" name="telephone"
                                            placeholder="Téléphone (ex: 621554784)" aria-describedby="telephoneFeedback"
                                            required inputmode="tel" autocomplete="tel">
                                    </div>
                                    <div class="invalid-feedback" id="telephoneFeedback">Veuillez entrer un numéro de
                                        téléphone valide.</div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="email" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="email@exemple.com" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="subject" class="form-label">Sujet</label>
                                    <input type="text" class="form-control" id="subject" name="subject"
                                        placeholder="Sujet de votre message" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" placeholder="Votre message" rows="5" required></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" id="submitBtn" class="btn btn-success">Envoyer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5 col-sm-12">
                    <div class="contact-info-left p-4 bg-white shadow-sm rounded">
                        <h4 class="mb-3">Informations de contact</h4>
                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-success"></i><strong>Adresse :</strong>
                            Nongo — Carrefour Morykantéya, Conakry, Guinée</p>
                        <p class="mb-2"><i class="fas fa-phone me-2 text-success"></i><strong>Téléphone :</strong> <a
                                href="tel:+224623248567">+224 623 24 85 67</a></p>
                        <p class="mb-2"><i class="fas fa-envelope me-2 text-success"></i><strong>Email :</strong> <a
                                href="mailto:mourimagro@gmail.com">mourima.enterprise@gmail.com</a></p>

                        <h5 class="mt-4">Horaires</h5>
                        <ul class="list-unstyled mb-3">
                            <li>Lundi — Vendredi: 08:00 — 18:00</li>
                            <li>Samedi: 09:00 — 13:00</li>
                            <li>Dimanche: Fermé</li>
                        </ul>

                        <h5>Réseaux sociaux</h5>
                        <p>
                            <a href="#" class="me-2"><i class="fab fa-facebook fa-lg"></i></a>
                            <a href="#" class="me-2"><i class="fab fa-instagram fa-lg"></i></a>
                            <a href="#"><i class="fab fa-whatsapp fa-lg"></i></a>
                        </p>

                        <div class="mt-3">
                            <h6>Localisation</h6>
                            <div class="ratio ratio-16x9 rounded overflow-hidden">
                                <iframe src="https://www.google.com/maps?q=Nongo%20Conakry&output=embed" style="border:0;"
                                    allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Contact Us -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const alertBox = document.getElementById('contactAlert');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validation simple
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const message = document.getElementById('message').value.trim();

                if (!name || !email || !message) {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'Veuillez remplir au moins votre nom, email et message.';
                    alertBox.classList.remove('d-none');
                    return;
                }

                // Désactiver le bouton pendant l'envoi (simulé ici)
                submitBtn.disabled = true;
                submitBtn.textContent = 'Envoi...';

                // Simulation d'envoi (remplacez par fetch vers une route si vous avez un endpoint)
                setTimeout(function() {
                    alertBox.className = 'alert alert-success';
                    alertBox.textContent =
                        'Merci — votre message a été envoyé. Nous vous contacterons bientôt.';
                    alertBox.classList.remove('d-none');
                    form.reset();
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Envoyer';
                }, 800);
            });
        });
    </script>

    <style>
        .contact-box-main {
            background: #f8f9fa;
        }

        .contact-form-right .form-control:focus {
            box-shadow: none;
            border-color: #1c911e;
        }

        .contact-info-left i {
            color: #1c911e;
        }
    </style>

@endsection
