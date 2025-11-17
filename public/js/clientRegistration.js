/**
 * Fonction globale pour mettre à jour le panneau "Moi" avec une structure cohérente
 * Utilisée par clientRegistration.js et profile_modal.blade.php
 * @param {Object} userData - Les données utilisateur/client avec prenom, nom, email, tel, whatsapp, adresse, photo
 */
function updateMoiPanel(userData) {
    try {
        if (!userData) return false;

        const sideContainer = document.getElementById('client-info-content');
        if (!sideContainer) return false;

        const fullName = ((userData.prenom ? userData.prenom + ' ' : '') + (userData.nom || '')).trim();

        let photoHtml;
        if (userData.photo) {
            photoHtml = `<img src="/storage/${userData.photo}" alt="Photo" class="rounded-circle" style="width:100px; height:100px; object-fit:cover;">`;
        } else {
            // Générer les initiales à partir du nom et prénom
            const names = fullName.split(' ').filter(n => n.length > 0);
            let initials = '';
            if (names.length >= 2) {
                initials = (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
            } else if (names.length === 1) {
                initials = names[0].charAt(0).toUpperCase();
            } else {
                initials = 'U';
            }
            photoHtml = `<div class="default-avatar rounded-circle d-flex align-items-center justify-content-center" style="width:100px; height:100px; margin:0 auto; background-color:#1c911e;"><span style="font-size:2rem; font-weight:bold; color:white;">${initials}</span></div>`;
        }

        const csrfToken = document.querySelector('meta[name=csrf-token]') ?
            document.querySelector('meta[name=csrf-token]').getAttribute('content') : '';

        sideContainer.innerHTML = `
            <div class="user-info">
                <div class="text-center mb-3">${photoHtml}</div>
                <div class="text-center mb-2">
                    <h5 class="fw-bold mb-1" style="font-size:1.1rem;">${fullName || 'Utilisateur'}</h5>
                    <small class="text-muted">${userData.email || ''}</small>
                </div>
                <div class="info-list">
                    <div class="info-item mb-2">
                        <i class="fas fa-phone-alt text-success"></i>
                        <span>${userData.tel || 'Non renseigné'}</span>
                    </div>
                    <div class="info-item">
                        <i class="fab fa-whatsapp text-success"></i>
                        <span>${userData.whatsapp || 'Non renseigné'}</span>
                    </div>
                    <div class="info-item">
                        <div class="info-link">
                            <i class="fas fa-map-marker-alt text-success"></i>
                            <span>${userData.adresse || 'Non renseignée'}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" id="btnEditProfile" class="btn btn-outline-success btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#editProfileModal"><i class="fas fa-user-edit"></i> Modifier mes informations</button>
                    <form action="/logout" method="POST" class="w-100"><input type="hidden" name="_token" value="${csrfToken}"><button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="fas fa-sign-out-alt"></i> Déconnexion</button></form>
                </div>
            </div>
        `;

        return true;
    } catch (e) {
        console.warn('Erreur updateMoiPanel:', e);
        return false;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const clientForm = document.getElementById('clientRegistrationForm');
    const clientInfoDiv = document.getElementById('clientInfo');
    const btnCommander = document.getElementById('btn-commander');

    // Fonction pour vérifier si un client est connecté
    function isClientLoggedIn() {
        return localStorage.getItem('clientInfo') !== null;
    }

    // Element d'erreur d'inscription (placé en haut du formulaire)
    const registrationErrorDiv = document.getElementById('registration-error');

    function showRegistrationError(msg) {
        if (registrationErrorDiv) {
            registrationErrorDiv.textContent = msg;
            registrationErrorDiv.classList.remove('d-none');
            registrationErrorDiv.classList.remove('alert-success');
            registrationErrorDiv.classList.add('alert-danger');
            try { registrationErrorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (e) { /* ignore */ }
        } else {
            if (typeof showToast === 'function') showToast(msg);
            else alert(msg);
        }
    }

    function hideRegistrationError() {
        if (registrationErrorDiv) {
            registrationErrorDiv.classList.add('d-none');
            registrationErrorDiv.textContent = '';
        }
    }

    // Écouter les changements d'authentification (connexion/déconnexion)
    window.addEventListener('clientInfoChanged', function (e) {
        // e.detail == null => déconnexion
        if (!e.detail) {
            if (clientInfoDiv) clientInfoDiv.style.display = 'none';
            if (clientForm) clientForm.style.display = 'block';
            updateCommanderButton();
            // Nettoyer le panneau 'Moi' si le serveur ne l'a pas rendu
            try {
                const sideContainer = document.getElementById('client-info-content');
                if (sideContainer && !sideContainer.querySelector('.user-info')) {
                    sideContainer.innerHTML = `<li class="text-center text-muted">Vous n\'êtes pas connecté.</li>`;
                }
            } catch (err) {
                console.warn('Erreur nettoyage panneau Moi', err);
            }
            return;
        }

        // e.detail devrait être au format { client: { ... } }
        let payload = e.detail;
        // Si payload est au format plat { nom, tel, ... }, le normaliser
        if (payload && !payload.client && (payload.nom || payload.tel)) {
            payload = { client: payload };
        }

        try {
            showClientInfo(payload);
        } catch (err) {
            console.error('Erreur lors de l\'affichage des infos client depuis l\'événement :', err);
        }
    });

    // Fonction pour activer/désactiver le bouton de commande
    function updateCommanderButton() {
        if (btnCommander) {
            if (isClientLoggedIn()) {
                btnCommander.classList.remove('d-none');
            } else {
                btnCommander.classList.add('d-none');
            }
        }
    }

    // Mettre à jour le bouton au chargement
    updateCommanderButton();

    // Gestion du bouton commander (protection contre double attachement)
    if (btnCommander) {
        if (!btnCommander.dataset.listenerAttached) {
            btnCommander.dataset.listenerAttached = '1';

            let commanderInFlight = false;
            const originalBtnHtml = btnCommander.innerHTML;

            btnCommander.addEventListener('click', function () {
                if (commanderInFlight) return;

                if (!isClientLoggedIn()) {
                    alert('Veuillez vous inscrire avant de passer commande.');
                    return;
                }

                const selectedProducts = [];
                document.querySelectorAll('.select-produit:checked').forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const productName = row.querySelector('td:nth-child(2) span').textContent;
                    const quantity = row.querySelector('.qty').value;
                    const price = row.querySelector('.total').textContent.replace(/\s/g, '');

                    selectedProducts.push({
                        nom: productName,
                        quantite: parseInt(quantity),
                        prix: parseInt(price)
                    });
                });

                if (selectedProducts.length === 0) {
                    alert('Veuillez sélectionner au moins un produit.');
                    return;
                }

                const clientData = JSON.parse(localStorage.getItem('clientInfo'));
                const commandeData = {
                    client_id: clientData.client.id,
                    produits: selectedProducts,
                    total: document.getElementById('grand-total').textContent.replace(/\s/g, '')
                };

                // Ajouter un identifiant de commande côté client (idempotence)
                try {
                    let clientOrderUuid = sessionStorage.getItem('clientOrderUuid');
                    if (!clientOrderUuid) {
                        clientOrderUuid = 'co-' + Math.random().toString(36).slice(2, 10) + '-' + Date.now().toString(36);
                        sessionStorage.setItem('clientOrderUuid', clientOrderUuid);
                    }
                    commandeData.client_order_uuid = clientOrderUuid;
                } catch (e) {
                    console.warn('Impossible d\'utiliser sessionStorage pour clientOrderUuid', e);
                }

                // Marquer en cours et désactiver le bouton pour éviter la double-soumission
                commanderInFlight = true;
                btnCommander.disabled = true;
                btnCommander.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Envoi...';

                // Envoyer la commande au serveur
                fetch('/commandes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(commandeData)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // récupérer l'id de la commande (plusieurs formats possibles côté serveur)
                            const factureId = data.commande_id || (data.commande && data.commande.id);
                            if (factureId) {
                                window.location.href = `/facture/${factureId}`;
                                return;
                            }
                            // fallback : recharger la page si pas d'id
                            window.location.reload();
                            return;
                        } else {
                            const msg = data.message || 'Erreur lors de la création de la commande.';
                            if (typeof showToast === 'function') showToast(msg);
                            else alert(msg);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        try {
                            if (typeof showToast === 'function') showToast('Une erreur est survenue lors de la création de la commande.');
                            else console.error('Erreur création commande:', error);
                        } catch (e) {
                            console.error('Erreur affichage toast:', e);
                        }
                    })
                    .finally(() => {
                        // Réactiver le bouton si on n'a pas redirigé
                        commanderInFlight = false;
                        btnCommander.disabled = false;
                        btnCommander.innerHTML = originalBtnHtml;
                    });
            });
        }
    }

    // Fonction pour afficher les informations du client
    function showClientInfo(payload) {
        if (clientForm) clientForm.style.display = 'none';

        // Normalize payload: support { client: {...} } or a flat object
        const client = payload && payload.client ? payload.client : (payload || {});

        // store normalized clientInfo to localStorage for reuse
        try { localStorage.setItem('clientInfo', JSON.stringify({ client })); } catch (e) { /* ignore */ }

        if (clientInfoDiv) {
            clientInfoDiv.innerHTML = `
                <div class="alert alert-success">
                    <h4 class="mb-3">Informations du client</h4>
                    <div class="client-details">
                        <div class="row g-2">
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-user me-2" style="color: #1c911e;"></i>
                                    <span>${client.nom || ''}</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-phone me-2" style="color: #1c911e;"></i>
                                    <span>${client.tel || ''}</span>
                                </div>
                            </div>
                            ${client.whatsapp ? `
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fab fa-whatsapp me-2" style="color: #1c911e;"></i>
                                    <span>${client.whatsapp}</span>
                                </div>
                            </div>` : ''}
                            ${client.adresse ? `
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-map-marker me-2" style="color: #1c911e;"></i>
                                    <span>${client.adresse}</span>
                                </div>
                            </div>` : ''}
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button id="modifyBtn" class="btn btn-success btn-sm">
                            <i class="fa fa-edit"></i> Modifier
                        </button>
                        <button id="logoutBtn" class="btn btn-danger btn-sm ms-2">
                            <i class="fa fa-sign-out"></i> Déconnexion
                        </button>
                    </div>
                </div>
            `;
            clientInfoDiv.style.display = 'block';
            updateCommanderButton(); // Activer le bouton commander

            // Déconnexion
            const logoutBtnEl = document.getElementById('logoutBtn');
            if (logoutBtnEl) {
                logoutBtnEl.onclick = function () {
                    try { localStorage.clear(); } catch (e) { console.warn('localStorage clear failed', e); }
                    window.dispatchEvent(new CustomEvent('clientInfoChanged', { detail: null }));
                    window.location.reload();
                };
            }

            // Modification inline
            const modifyBtnEl = document.getElementById('modifyBtn');
            if (modifyBtnEl) {
                modifyBtnEl.onclick = function () {
                    clientInfoDiv.style.display = 'none';
                    if (clientForm) clientForm.style.display = 'block';
                    try {
                        const setIf = (selector, value) => {
                            const input = document.querySelector(selector);
                            if (input) input.value = value || '';
                        }
                        setIf('#nom', client.nom);
                        setIf('#tel', client.tel || client.whatsapp);
                        setIf('#whatsapp', client.whatsapp || client.tel);
                        setIf('#adresse', client.adresse);
                    } catch (err) {
                        console.warn('Erreur pré-remplissage formulaire:', err);
                    }
                };
            }
            try { clientForm.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (err) { /* ignore */ }
            return;
        }

        // Mettre à jour aussi le panneau "Moi" dans la navbar si présent
        updateMoiPanel(client);
    }

    // Vérifier s'il y a déjà des informations client stockées
    const storedClientInfo = localStorage.getItem('clientInfo');
    if (storedClientInfo) {
        const clientData = JSON.parse(storedClientInfo);
        showClientInfo(clientData);
    }

    // Gestion du bouton "Modifier" en haut de la page (si présent)
    const topModifyBtn = document.getElementById('topModifyBtn');
    if (topModifyBtn) {
        topModifyBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const stored = localStorage.getItem('clientInfo');
            if (stored) {
                // Pré-remplir le formulaire et l'afficher
                const clientData = JSON.parse(stored);
                if (clientForm) clientForm.style.display = 'block';
                if (clientInfoDiv) clientInfoDiv.style.display = 'none';
                document.getElementById('nom').value = clientData.client.nom || '';
                document.getElementById('tel').value = clientData.client.tel || '';
                if (clientData.client.whatsapp) document.getElementById('whatsapp').value = clientData.client.whatsapp;
                if (clientData.client.adresse) document.getElementById('adresse').value = clientData.client.adresse;
                const setIf = (selector, value) => {
                    const input = clientForm.querySelector(selector);
                    if (input) input.value = value || '';
                }
                setIf('#nom', clientData.client.nom);
                setIf('#tel', clientData.client.tel);
                setIf('#whatsapp', clientData.client.whatsapp);
                setIf('#adresse', clientData.client.adresse);
                try { clientForm.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (err) { /* ignore */ }
                return;
            }
            // Si aucune info côté client, mais utilisateur serveur authentifié -> ouvrir modal profil
            const userRoleMeta = document.querySelector('meta[name="user-role"]');
            const userRole = userRoleMeta ? userRoleMeta.getAttribute('content') : '';
            if (userRole) {
                const modalEl = document.getElementById('editProfileModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    return;
                }
            }

            // Sinon, afficher le formulaire d'inscription vide
            if (clientForm) clientForm.style.display = 'block';
            if (clientInfoDiv) clientInfoDiv.style.display = 'none';
        });
    }

    if (clientForm) {
        clientForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(clientForm);

            fetch('/clients', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(async response => {
                    // essaye de parser le JSON (Laravel renvoie JSON en cas d'erreur de validation)
                    let payload = null;
                    try { payload = await response.json(); } catch (e) { payload = null; }

                    if (response.ok) {
                        // Réponse 200-299 : le serveur a traité la requête
                        if (payload && payload.success) {
                            // Inscription réussie -> stocker et mettre à jour l'UI
                            localStorage.setItem('clientInfo', JSON.stringify(payload));
                            window.dispatchEvent(new CustomEvent('clientInfoChanged', { detail: payload }));
                            hideRegistrationError();
                            showClientInfo(payload);
                        } else {
                            // Cas particulier où le serveur renvoie 200 mais indique un conflit
                            const msg = (payload && payload.message) ? payload.message : 'Inscription traitée.';
                            try { showRegistrationError(msg); } catch (e) { try { if (typeof showToast === 'function') showToast(msg); else alert(msg); } catch (ee) { alert(msg); } }

                            // Si le payload indique explicitement que le compte existe, ouvrir le modal
                            const indicatesExists = (payload && (payload.code === 'exists' || (payload.message && payload.message.toLowerCase().includes('déjà'))));
                            if (indicatesExists) {
                                const connexionModalEl = document.getElementById('connexionModal');
                                if (window.OPEN_CONNEXION_ON_REGISTER === true && connexionModalEl && typeof bootstrap !== 'undefined') {
                                    const modal = new bootstrap.Modal(connexionModalEl);
                                    modal.show();
                                }
                            }
                        }
                    } else {
                        // Gestion des erreurs côté serveur (statuts non 2xx)
                        // 422 -> erreurs de validation (par ex. tel unique)
                        if (response.status === 422 && payload && payload.errors && payload.errors.tel) {
                            const msg = 'Ce numéro est déjà enregistré — veuillez vous connecter à la place.';
                            try { showRegistrationError(msg); } catch (e) { try { if (typeof showToast === 'function') showToast(msg); else alert(msg); } catch (ee) { alert(msg); } }

                            // Ouvrir le modal de connexion seulement dans ce cas précis
                            const connexionModalEl = document.getElementById('connexionModal');
                            if (window.OPEN_CONNEXION_ON_REGISTER === true && connexionModalEl && typeof bootstrap !== 'undefined') {
                                const modal = new bootstrap.Modal(connexionModalEl);
                                modal.show();
                            }
                        } else if (response.status === 409 || (payload && payload.code === 'exists')) {
                            // 409 Conflict ou code explicite
                            const msg = (payload && payload.message) ? payload.message : 'Ce numéro est déjà enregistré.';
                            try { showRegistrationError(msg); } catch (e) { try { if (typeof showToast === 'function') showToast(msg); else alert(msg); } catch (ee) { alert(msg); } }

                            const connexionModalEl = document.getElementById('connexionModal');
                            if (window.OPEN_CONNEXION_ON_REGISTER === true && connexionModalEl && typeof bootstrap !== 'undefined') {
                                const modal = new bootstrap.Modal(connexionModalEl);
                                modal.show();
                            }
                        } else {
                            const msg = (payload && payload.message) ? payload.message : 'Une erreur est survenue lors de l\'enregistrement.';
                            try { showRegistrationError(msg); } catch (e) { try { if (typeof showToast === 'function') showToast(msg); else alert(msg); } catch (ee) { alert(msg); } }
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    const msg = 'Une erreur est survenue lors de l\'enregistrement.';
                    try { showRegistrationError(msg); } catch (e) { try { if (typeof showToast === 'function') showToast(msg); else alert(msg); } catch (ee) { alert(msg); } }
                });
        });
    }
});
