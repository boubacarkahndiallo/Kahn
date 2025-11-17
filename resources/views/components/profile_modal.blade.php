<!-- Modal Édition Profil Utilisateur -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">

            <!-- Decorative header -->
            <div class="profile-modal-header"
                style="background: linear-gradient(135deg,#28a745 0%,#20c997 100%); height:64px; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
                <button type="button" class="btn-close btn-close-white position-absolute end-3" data-bs-dismiss="modal"
                    aria-label="Fermer" style="top:10px; right:12px;"></button>
                <div class="text-white text-center position-absolute start-50 translate-middle-x"
                    style="top:18px; font-weight:600; font-size:0.95rem;">
                    <i class="fas fa-user-circle me-2"></i> Mon profil
                </div>
            </div>

            <form id="editProfileForm" enctype="multipart/form-data" class="bg-white">
                @csrf
                @method('PUT')

                <!-- Avatar (overlaps header) -->
                <div class="text-center" style="margin-top:-60px;">
                    <div class="position-relative d-inline-block">
                        <img id="profilePhotoPreview"
                            src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22140%22 height=%22140%22 viewBox=%220 0 140 140%22%3E%3Ccircle cx=%2270%22 cy=%2270%22 r=%2770%22 fill=%22%23e9ecef%22/%3E%3Ctext x=%2270%22 y=%2280%22 text-anchor=%22middle%22 font-size=%2248%22 fill=%22%23999%22%3E%F0%9F%91%A4%3C/text%3E%3C/svg%3E' }}"
                            alt="Photo de profil" class="rounded-circle"
                            style="width:130px; height:130px; object-fit:cover; border:5px solid #ffffff; box-shadow:0 10px 30px rgba(0,0,0,0.12); cursor:pointer;">
                        <div class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2"
                            style="transform:translate(10%,10%); cursor:pointer;">
                            <i class="fas fa-camera text-white" style="font-size:1rem"></i>
                        </div>
                    </div>
                    <input type="file" class="form-control d-none" id="profilePhoto" name="photo" accept="image/*">
                    <p class="text-center text-muted small mt-2 mb-0">Cliquez sur la photo pour la modifier</p>
                    <small class="text-danger d-none" id="error-photo"></small>
                </div>

                <div class="modal-body pt-3">
                    <div id="profileMessage" class="alert d-none" role="alert"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="profilePrenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control form-control" id="profilePrenom" name="prenom"
                                value="{{ auth()->user()->prenom ?? '' }}">
                            <small class="text-danger d-none" id="error-prenom"></small>
                        </div>

                        <div class="col-md-6">
                            <label for="profileNom" class="form-label">Nom</label>
                            <input type="text" class="form-control form-control" id="profileNom" name="nom"
                                value="{{ auth()->user()->nom ?? '' }}">
                            <small class="text-danger d-none" id="error-nom"></small>
                        </div>

                        <div class="col-md-12">
                            <label for="profileEmail" class="form-label">Email</label>
                            <input type="email" class="form-control form-control" id="profileEmail" name="email"
                                value="{{ auth()->user()->email ?? '' }}">
                            <small class="text-danger d-none" id="error-email"></small>
                        </div>

                        <div class="col-md-6">
                            <label for="profileTel" class="form-label">Téléphone</label>
                            <input type="text" class="form-control form-control" id="profileTel" name="tel"
                                value="{{ auth()->user()->tel ?? '' }}" placeholder="+224 ...">
                            <small class="text-danger d-none" id="error-tel"></small>
                        </div>

                        <div class="col-md-6">
                            <label for="profileWhatsapp" class="form-label">WhatsApp</label>
                            <input type="text" class="form-control form-control" id="profileWhatsapp" name="whatsapp"
                                value="{{ auth()->user()->whatsapp ?? '' }}" placeholder="+224 ...">
                            <small class="text-danger d-none" id="error-whatsapp"></small>
                        </div>

                        <div class="col-md-12">
                            <label for="profileAdresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control form-control" id="profileAdresse"
                                name="adresse" value="{{ auth()->user()->adresse ?? '' }}">
                            <small class="text-danger d-none" id="error-adresse"></small>
                        </div>

                        <div class="col-md-6 mt-2">
                            <label for="profilePassword" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="profilePassword" name="password"
                                placeholder="Laissez vide pour conserver le mot de passe actuel">
                            <small class="text-muted d-none" id="password-help-text">Minimum 4 caractères.</small>
                            <small class="text-danger d-none" id="error-password"></small>
                        </div>

                        <div class="col-md-6 mt-2">
                            <label for="profilePasswordConfirm" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="profilePasswordConfirm"
                                name="password_confirmation" placeholder="Confirmez le mot de passe">
                            <small class="text-danger d-none" id="error-password_confirmation"></small>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-outline-danger btn rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success btn rounded-pill" id="btnSaveProfile">
                        <span id="btnText">Enregistrer</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"
                            aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editProfileForm');
        const modal = new bootstrap.Modal(document.getElementById('editProfileModal'));
        const messageDiv = document.getElementById('profileMessage');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');

        // Photo input & preview handlers (accessibilité + prévisualisation)
        const photoInput = document.getElementById('profilePhoto');
        const photoPreview = document.getElementById('profilePhotoPreview');
        if (photoPreview) {
            // keyboard + screen reader affordance
            photoPreview.setAttribute('tabindex', '0');
            photoPreview.setAttribute('role', 'button');
            photoPreview.setAttribute('aria-label', 'Modifier la photo de profil');
            // clicking the image opens file picker
            photoPreview.addEventListener('click', function() {
                if (photoInput) photoInput.click();
            });
            // allow Enter / Space to open picker
            photoPreview.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (photoInput) photoInput.click();
                }
            });
        }

        if (photoInput) {
            photoInput.addEventListener('change', function(e) {
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                // only image files
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    if (photoPreview) photoPreview.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        // Ouvrir le modal quand le bouton "Modifier" est cliqué
        const editButton = document.getElementById('btnEditProfile');
        if (editButton) {
            editButton.addEventListener('click', function(e) {
                e.preventDefault();
                modal.show();
            });
        }

        // Gestion du champ mot de passe - afficher le message d'aide uniquement si rempli
        const passwordInput = document.getElementById('profilePassword');
        const passwordHelpText = document.getElementById('password-help-text');
        if (passwordInput && passwordHelpText) {
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    passwordHelpText.classList.remove('d-none');
                } else {
                    passwordHelpText.classList.add('d-none');
                }
            });
        }

        // Soumettre le formulaire en AJAX
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Réinitialiser les messages d'erreur
            document.querySelectorAll('[id^="error-"]').forEach(el => {
                el.classList.add('d-none');
                el.textContent = '';
            });
            messageDiv.classList.add('d-none');
            messageDiv.classList.remove('alert-success', 'alert-danger');

            // Afficher le spinner
            btnSpinner.classList.remove('d-none');
            btnText.textContent = 'Enregistrement...';

            const formData = new FormData(form);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document
                    .querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
                // Use POST with _method=PUT for reliable file upload handling
                formData.append('_method', 'PUT');
                const response = await fetch('/moi', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok) {
                    // Erreurs de validation
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorEl = document.getElementById(`error-${field}`);
                            if (errorEl) {
                                errorEl.textContent = data.errors[field][0];
                                errorEl.classList.remove('d-none');
                            }
                        });
                    }
                    if (data.message) {
                        messageDiv.textContent = data.message;
                        messageDiv.classList.remove('d-none');
                        messageDiv.classList.add('alert-danger');
                    }
                } else {
                    // Succès
                    messageDiv.textContent = data.message || 'Profil mis à jour avec succès !';
                    messageDiv.classList.remove('d-none');
                    messageDiv.classList.add('alert-success');

                    // Mettre à jour les champs du formulaire avec les nouvelles données
                    if (data.user) {
                        const user = data.user;
                        document.getElementById('profilePrenom').value = user.prenom || '';
                        document.getElementById('profileNom').value = user.nom || '';
                        document.getElementById('profileEmail').value = user.email || '';
                        document.getElementById('profileTel').value = user.tel || '';
                        document.getElementById('profileWhatsapp').value = user.whatsapp || '';
                        document.getElementById('profileAdresse').value = user.adresse || '';

                        // Si une photo a été changée, mettre à jour l'aperçu
                        if (user.photo) {
                            document.getElementById('profilePhotoPreview').src = '/storage/' + user
                                .photo;
                        }
                    }

                    // Effacer les champs de mot de passe
                    document.getElementById('profilePassword').value = '';
                    document.getElementById('profilePasswordConfirm').value = '';

                    // Mettre à jour le panneau "Moi" dans la navbar en utilisant la fonction globale
                    if (data.user && typeof updateMoiPanel === 'function') {
                        updateMoiPanel(data.user);
                    }

                    // Supprimer le clientInfo côté navigateur si présent
                    try {
                        localStorage.removeItem('clientInfo');
                    } catch (e) {
                        /* ignore */
                    }

                    // Afficher un toast de succès
                    try {
                        if (typeof showToast === 'function') {
                            showToast('Profil mis à jour avec succès !');
                        }
                    } catch (err) {
                        console.warn('Toast non disponible', err);
                    }

                    // Fermer le modal après 2 secondes
                    setTimeout(() => {
                        modal.hide();
                    }, 2000);
                }
            } catch (error) {
                console.error('Erreur:', error);
                messageDiv.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                messageDiv.classList.remove('d-none');
                messageDiv.classList.add('alert-danger');
            } finally {
                // Masquer le spinner
                btnSpinner.classList.add('d-none');
                btnText.textContent = 'Enregistrer';
            }
        });
    });
</script>
