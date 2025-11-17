// public/js/panier.js
document.addEventListener("DOMContentLoaded", function () {

    // Fonction pour sauvegarder l'état des produits sélectionnés
    function saveSelectedProducts() {
        const selectedProducts = [];
        document.querySelectorAll('#produits-table tbody tr').forEach(tr => {
            const checkbox = tr.querySelector('.select-produit');
            const qtyInput = tr.querySelector('.qty');
            if (checkbox && checkbox.checked) {
                selectedProducts.push({
                    id: tr.dataset.id,
                    qty: qtyInput ? parseInt(qtyInput.value) : 1
                });
            }
        });
        localStorage.setItem('selectedProducts', JSON.stringify(selectedProducts));
    }

    // Fonction pour restaurer l'état des produits sélectionnés
    function restoreSelectedProducts() {
        const savedProducts = localStorage.getItem('selectedProducts');
        if (savedProducts) {
            const products = JSON.parse(savedProducts);
            products.forEach(product => {
                const tr = document.querySelector(`#produits-table tbody tr[data-id="${product.id}"]`);
                if (tr) {
                    const checkbox = tr.querySelector('.select-produit');
                    const qtyInput = tr.querySelector('.qty');
                    if (checkbox) {
                        checkbox.checked = true;
                        if (qtyInput) {
                            qtyInput.value = product.qty;
                            qtyInput.disabled = false;
                        }
                    }
                }
            });
            // Mettre à jour le panier après restauration
            syncFromTable();
        }
    }

    const { jsPDF } = window.jspdf || {}; // pour le lien du pdf dans le panier
    const sideCartList = document.querySelector('.side .cart-list');
    const cartCount = document.getElementById('cart-count');
    const inscriptionForm = document.getElementById('inscriptionForm'); // formulaire
    const inscriptionModal = document.getElementById('inscriptionModal'); // modal
    let produits = [];

    // Si les éléments critiques n'existent pas (pas sur la page allproduit), on charge le panier depuis localStorage
    const produitTableExists = document.getElementById('produits-table') !== null;

    // Charger le panier depuis localStorage au démarrage si la table n'existe pas
    function loadPanierFromLocalStorage() {
        const savedPanier = localStorage.getItem('panier');
        if (savedPanier) {
            try {
                produits = JSON.parse(savedPanier);
                if (sideCartList) {
                    updateSideCart();
                }
            } catch (e) {
                console.warn('Erreur chargement panier localStorage', e);
            }
        }
    }

    // Si la table n'existe pas, charger depuis localStorage
    if (!produitTableExists) {
        loadPanierFromLocalStorage();
    }

    // Fonction pour sauvegarder le panier dans localStorage
    function savePanierToLocalStorage() {
        localStorage.setItem('panier', JSON.stringify(produits));
    }

    // Fonction pour mettre à jour cartCount partout
    function updateCartCount() {
        if (cartCount) {
            cartCount.textContent = produits.length;
        }
    }

    // -----------------------------
    // Gestion utilisateur connecté persistante (priorise clientInfo)
    // -----------------------------
    let savedUser = null;
    const rawClientInfo = localStorage.getItem('clientInfo');
    if (rawClientInfo) {
        try {
            const parsed = JSON.parse(rawClientInfo);
            // `clientInfo` contient la clé `client` (structure renvoyée par clientRegistration.js)
            if (parsed && parsed.client) {
                savedUser = parsed.client;
                // conserver compatibilité avec l'ancien nom "authUser"
                localStorage.setItem('authUser', JSON.stringify(savedUser));
            }
        } catch (e) {
            console.warn('clientInfo invalide dans localStorage', e);
        }
    }

    if (!savedUser) {
        const rawAuth = localStorage.getItem('authUser');
        if (rawAuth) {
            try {
                savedUser = JSON.parse(rawAuth);
            } catch (e) {
                console.warn('authUser invalide dans localStorage', e);
            }
        }
    }

    if (savedUser) {
        window.authUser = savedUser;
    }

    const isLoggedIn = typeof window.authUser !== 'undefined' && window.authUser !== null;
    const user = window.authUser;

    // Fonction pour désactiver/activer les checkboxes et quantités selon l'état de connexion
    function updateProductSelectionState() {
        const checkboxes = document.querySelectorAll('.select-produit');
        const qtyInputs = document.querySelectorAll('.qty');
        const commanderBtn = document.querySelector('.btn-commander');

        if (!isLoggedIn) {
            // Désactiver les checkboxes et inputs
            checkboxes.forEach(checkbox => {
                checkbox.disabled = true;
                checkbox.checked = false;
                checkbox.title = "Veuillez vous inscrire pour sélectionner des produits";
            });
            qtyInputs.forEach(input => {
                input.disabled = true;
                input.value = 1;
            });
            if (commanderBtn) {
                commanderBtn.disabled = true;
                commanderBtn.title = "Veuillez vous inscrire pour commander";
            }
            // Vider le panier
            localStorage.removeItem('selectedProducts');
            produits = [];
            updateSideCart();
        } else {
            // Activer les checkboxes et inputs
            checkboxes.forEach(checkbox => {
                checkbox.disabled = false;
                checkbox.title = "";
            });
            qtyInputs.forEach(input => {
                input.disabled = true; // Reste désactivé jusqu'à ce que le checkbox correspondant soit coché
            });
            if (commanderBtn) {
                commanderBtn.disabled = false;
                commanderBtn.title = "";
            }
        }
    }

    // Appeler la fonction au chargement et après inscription/déconnexion
    updateProductSelectionState();

    // Observer les changements d'état de connexion
    window.addEventListener('authStateChanged', updateProductSelectionState);

    function updateSideCart() {
        // Vérifier que sideCartList existe
        if (!sideCartList) return;

        sideCartList.querySelectorAll('li:not(.total)').forEach(li => li.remove());

        produits.forEach((p, index) => {
            const li = document.createElement('li');
            li.innerHTML = `
                <a href="#" class="photo"><img src="${p.image}" class="cart-thumb" alt="${p.nom}" /></a>
                <h6><a href="#">${p.nom}</a></h6>
                <p>${p.qty}x - <span class="price">${new Intl.NumberFormat('fr-FR').format(p.total)} GNF</span>
                    <button class="btn btn-sm btn-danger float-end btn-annuler" data-index="${index}">
                        <i class="fa fa-times"></i>
                    </button>
                </p>`;
            sideCartList.insertBefore(li, sideCartList.querySelector('.total'));
        });

        const totalEl = sideCartList.querySelector('.total .float-right');
        if (totalEl) {
            totalEl.innerHTML =
                `<strong>Total</strong>: ${new Intl.NumberFormat('fr-FR').format(produits.reduce((sum, p) => sum + p.total, 0))} GNF`;
        }

        updateCartCount();
        savePanierToLocalStorage();

        sideCartList.querySelectorAll('.btn-annuler').forEach(btn => {
            btn.addEventListener('click', function () {
                const i = parseInt(this.dataset.index);
                const removedProduit = produits.splice(i, 1)[0];

                const tableRow = document.querySelector(`#produits-table tbody tr[data-prix="${removedProduit.prix}"]`);
                if (tableRow) {
                    const chk = tableRow.querySelector('.select-produit');
                    const qtyInput = tableRow.querySelector('.qty');
                    if (chk) chk.checked = false;
                    if (qtyInput) qtyInput.value = 0;
                }
                updateSideCart();
            });
        });
    }

    function syncFromTable() {
        const table = document.getElementById('produits-table');
        produits = [];
        table.querySelectorAll('tbody tr').forEach(tr => {
            const chk = tr.querySelector('.select-produit');
            if (chk && chk.checked) {
                const nom = tr.querySelector('td:nth-child(2)').innerText.trim();
                const qty = parseInt(tr.querySelector('.qty').value) || 1;
                const prix = parseFloat(tr.dataset.prix);
                const total = prix * qty;
                const imageTag = tr.querySelector('td:nth-child(2) img');
                const image = imageTag ? imageTag.src : 'https://via.placeholder.com/60';
                const id = tr.dataset.id ?? tr.dataset.produitId ?? null;
                produits.push({ id, nom, qty, prix, total, image });
            }
        });
        updateSideCart();
    }

    document.querySelectorAll('#produits-table .select-produit').forEach(chk => {
        chk.addEventListener('change', function () {
            const qtyInput = this.closest('tr').querySelector('.qty');
            qtyInput.disabled = !this.checked;
            qtyInput.value = this.checked ? 1 : 0;
            syncFromTable();
            saveSelectedProducts();
        });
    });
    document.querySelectorAll('#produits-table .qty').forEach(input => {
        input.addEventListener('input', function () {
            syncFromTable();
            saveSelectedProducts();
        });
    });

    // Restaurer les produits sélectionnés au chargement de la page
    restoreSelectedProducts();

    // Afficher un toast Bootstrap avec un message (utilisé pour la copie/partage)
    function showToast(message) {
        try {
            const toastEl = document.getElementById('factureToast');
            const body = document.getElementById('factureToastBody');
            if (body) body.textContent = message;
            if (toastEl) {
                const bsToast = new bootstrap.Toast(toastEl);
                bsToast.show();
            } else {
                // Fallback alert
                alert(message);
            }
        } catch (err) {
            console.warn('Erreur showToast:', err);
            alert(message);
        }
    }

    // Afficher une alerte professionnelle sur la page (coin supérieur droit)
    function showAlert(type, message) {
        const alertContainer = document.createElement('div');
        alertContainer.className = `alert alert-${type} alert-dismissible fade show`;
        alertContainer.setAttribute('role', 'alert');
        alertContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
        alertContainer.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alertContainer);

        // Auto-dismiss après 5 secondes
        setTimeout(() => {
            if (alertContainer.parentNode) {
                alertContainer.classList.remove('show');
                setTimeout(() => {
                    if (alertContainer.parentNode) alertContainer.remove();
                }, 300);
            }
        }, 5000);
    }

    // Afficher un modal de confirmation de commande professionnel
    function showOrderConfirmationModal(produitsHTML, totalGeneral, client, callback) {
        const modalId = 'confirmationCommandeModal';
        let modal = document.getElementById(modalId);

        if (!modal) {
            modal = document.createElement('div');
            modal.id = modalId;
            modal.className = 'modal fade';
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('aria-hidden', 'true');
            document.body.appendChild(modal);
        }

        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header" style="background: linear-gradient(135deg, #1c911e 0%, #14680f 100%); border: none;">
                        <h5 class="modal-title text-white fw-bold">
                            <i class="fa fa-shopping-cart me-2"></i>Confirmation de Commande
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Vos produits :</h6>
                            <ul class="list-unstyled border-start border-4 ps-3" style="border-color: #1c911e;">
                                ${produitsHTML}
                            </ul>
                        </div>
                        <div class="bg-light p-3 rounded mb-4 border-start border-4" style="border-color: #1c911e;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total à payer :</span>
                                <span class="fw-bold fs-5" style="color: #1c911e;">${new Intl.NumberFormat('fr-FR').format(totalGeneral)} GNF</span>
                            </div>
                        </div>
                        <div class="alert alert-info mb-3" style="border-left: 4px solid #1c911e; background-color: rgba(28, 145, 30, 0.1);">
                            <i class="fa fa-info-circle me-2" style="color: #1c911e;"></i>
                            <strong>Client :</strong> ${client.nom}
                        </div>
                    </div>
                    <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #dee2e6;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn" style="background: linear-gradient(135deg, #1c911e 0%, #14680f 100%); color: white; border: none;" id="btn-confirmer-commande">
                            <i class="fa fa-check me-2"></i>Confirmer la commande
                        </button>
                    </div>
                </div>
            </div>
        `;

        const bsModal = new bootstrap.Modal(modal);
        modal.querySelector('#btn-confirmer-commande').onclick = function () {
            bsModal.hide();
            callback(true);
        };

        bsModal.show();
    }
    // ----------------------
    // Modification principale : envoi direct si connecté
    // ----------------------
    // const btnCommander = document.getElementById('btn-commander');
    // btnCommander.addEventListener('click', function () {
    //     syncFromTable();
    //     if (produits.length === 0) return;

    //     if (!isLoggedIn) {
    //         alert("Veuillez vous inscrire ou vous connecter avant de passer la commande.");
    //         inscriptionForm.scrollIntoView({ behavior: "smooth", block: "start" });
    //         const nomInput = inscriptionForm.querySelector('input[name="nom"]');
    //         if (nomInput) nomInput.focus({ preventScroll: true });
    //         return;
    //     }

    //     // Envoi direct
    //     envoyerCommande(user.id);
    // });

    const btnCommander = document.getElementById('btn-commander');
    // Empêcher l'attachement multiple du même gestionnaire (autre script peut aussi attacher)
    if (btnCommander && !btnCommander.dataset.listenerAttached) {
        btnCommander.dataset.listenerAttached = '1';
        // btnCommander.addEventListener('click', function () {
        //     syncFromTable();
        //     if (produits.length === 0) return;

        //     let message = "Vous avez passé une commande de :\n";
        //     let totalGeneral = 0;
        //     produits.forEach(p => {
        //         message += `${p.nom} : ${p.qty} x ${new Intl.NumberFormat('fr-FR').format(p.prix)} GNF = ${new Intl.NumberFormat('fr-FR').format(p.total)} GNF\n`;
        //         totalGeneral += p.total;
        //     });
        //     message += `Total à payer : ${new Intl.NumberFormat('fr-FR').format(totalGeneral)} GNF\nConfirmez-vous !`;
        //     if (confirm(message)) {
        //         ajouterBoutonConfirmer();
        //     }
        // });
        btnCommander.addEventListener('click', function () {
            syncFromTable();
            if (produits.length === 0) {
                showAlert('danger', '⚠️ Veuillez sélectionner au moins un produit avant de commander.');
                return;
            }

            // Récupérer le client depuis clientInfo (priorité) ou fallback window.authUser
            const clientInfoRaw = localStorage.getItem('clientInfo');
            let client = null;
            if (clientInfoRaw) {
                try { client = JSON.parse(clientInfoRaw).client; } catch (e) { client = null; }
            }
            if (!client && window.authUser) client = window.authUser;

            if (!client) {
                showAlert('danger', '⚠️ Veuillez d\'abord renseigner vos informations client avant de passer la commande !');
                const formEl = document.getElementById('clientRegistrationForm') || document.getElementById('inscriptionForm');
                if (formEl) formEl.scrollIntoView({ behavior: "smooth" });
                return;
            }

            // Construire le contenu HTML de la confirmation
            let totalGeneral = 0;
            let produitsHTML = '';
            produits.forEach(p => {
                produitsHTML += `<li class="mb-2"><strong>${p.nom}</strong> : ${p.qty} x ${new Intl.NumberFormat('fr-FR').format(p.prix)} GNF = <span class="text-success fw-bold">${new Intl.NumberFormat('fr-FR').format(p.total)} GNF</span></li>`;
                totalGeneral += p.total;
            });

            // Afficher le modal de confirmation professionnel
            showOrderConfirmationModal(produitsHTML, totalGeneral, client, function (confirmed) {
                if (confirmed) {
                    envoyerCommande(client.id);
                }
            });
        });
    } // fin guard dataset.listenerAttached

    function envoyerCommande(client_id = null) {
        // Construire payload JSON attendu par CommandeController
        const payload = {
            client_id: client_id,
            produits: produits.map(p => ({ nom: p.nom, qty: p.qty, prix: p.prix, total: p.total })),
            prix_total: produits.reduce((sum, p) => sum + p.total, 0),
            statut: 'en_cours'
        };

        // Générer ou réutiliser un UUID côté client pour assurer l'idempotence
        try {
            let clientOrderUuid = sessionStorage.getItem('clientOrderUuid');
            if (!clientOrderUuid) {
                clientOrderUuid = 'co-' + Math.random().toString(36).slice(2, 10) + '-' + Date.now().toString(36);
                sessionStorage.setItem('clientOrderUuid', clientOrderUuid);
            }
            payload.client_order_uuid = clientOrderUuid;
        } catch (e) {
            console.warn('Impossible d\'utiliser sessionStorage pour clientOrderUuid', e);
        }

        fetch("/commandes", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour les informations de la facture
                    const clientInfoRaw = localStorage.getItem('clientInfo');
                    const client = clientInfoRaw ? JSON.parse(clientInfoRaw).client : window.authUser;

                    document.getElementById('facture-client-info').innerHTML = `
                        <div class="ps-2">
                            <p class="mb-2"><i class="fas fa-user me-2 text-success"></i>${client.nom}</p>
                            <p class="mb-2"><i class="fas fa-phone me-2 text-success"></i>${client.tel}</p>
                            ${client.whatsapp ? `<p class="mb-2"><i class="fab fa-whatsapp me-2 text-success"></i>${client.whatsapp}</p>` : ''}
                            <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-success"></i>${client.adresse}</p>
                        </div>
                    `;

                    document.getElementById('facture-numero').textContent = data.commande.numero_commande;
                    document.getElementById('facture-date').textContent = new Date().toLocaleDateString('fr-FR');

                    // Remplir le tableau des produits (style compact, comme dans les modales Commande)
                    const tbody = document.getElementById('facture-produits');
                    tbody.innerHTML = produits.map(p => `
                        <tr>
                            <td class="text-start">${p.nom}</td>
                            <td class="text-center">${p.qty}</td>
                            <td class="text-end">${new Intl.NumberFormat('fr-FR').format(p.prix)} GNF</td>
                            <td class="text-end fw-bold text-success">${new Intl.NumberFormat('fr-FR').format(p.total)} GNF</td>
                        </tr>
                    `).join('');

                    document.getElementById('facture-total').textContent =
                        new Intl.NumberFormat('fr-FR').format(produits.reduce((sum, p) => sum + p.total, 0)) + ' GNF';

                    // Afficher le modal
                    const factureModal = new bootstrap.Modal(document.getElementById('factureModal'));
                    factureModal.show();

                    // Gestion de l'impression
                    document.getElementById('btn-imprimer-facture').onclick = function () {
                        window.print();
                    };

                    // Ajouter actions Appel / WhatsApp / Partage
                    try {
                        const companyTel = '+224623248567';
                        const companyWa = '224623248567';
                        const orderNum = data.commande.numero_commande || '';
                        // Construire le message texte
                        let msg = `Commande ${orderNum}\n`;
                        produits.forEach(p => {
                            msg += `${p.nom} x${p.qty} - ${new Intl.NumberFormat('fr-FR').format(p.total)} GNF\n`;
                        });
                        const totalText = new Intl.NumberFormat('fr-FR').format(produits.reduce((s, p) => s + p.total, 0)) + ' GNF';
                        msg += `Total: ${totalText}\n`;
                        const clientName = (client && client.nom) ? client.nom : '';
                        const clientTel = (client && client.tel) ? client.tel : '';
                        msg += `Client: ${clientName} ${clientTel ? '- Tel: ' + clientTel : ''}`;

                        // WhatsApp
                        const waHref = `https://wa.me/${companyWa}?text=` + encodeURIComponent(msg);
                        const btnWa = document.getElementById('btn-whatsapp');
                        if (btnWa) {
                            btnWa.setAttribute('href', waHref);
                            btnWa.setAttribute('target', '_blank');
                        }

                        // Partage (Web Share API ou copie)
                        const btnShare = document.getElementById('btn-share');
                        if (btnShare) {
                            btnShare.onclick = async function () {
                                const shareTitle = `Facture ${orderNum}`;
                                const shareText = msg + `\nContact: +224 623 24 85 67`;
                                if (navigator.share) {
                                    try {
                                        await navigator.share({ title: shareTitle, text: shareText });
                                    } catch (err) {
                                        console.warn('Partage annulé', err);
                                    }
                                } else if (navigator.clipboard) {
                                    try {
                                        await navigator.clipboard.writeText(shareText);
                                        showToast('Texte de la facture copié dans le presse-papiers.');
                                    } catch (err) {
                                        showToast('Impossible de copier la facture.');
                                    }
                                } else {
                                    showToast('Partage non supporté sur ce navigateur.');
                                }
                            };
                        }

                        // Bouton Appeler : déjà présent en HTML mais on s'assure du href
                        const btnCall = document.getElementById('btn-call');
                        if (btnCall) btnCall.setAttribute('href', 'tel:' + companyTel);
                    } catch (err) {
                        console.error('Erreur initialisation actions facture:', err);
                    }

                    // Mettre à jour l'affichage du panier sans décocher les éléments
                    syncFromTable();
                } else {
                    alert("Erreur lors de l’enregistrement de la commande.");
                }
            })
            .catch(err => {
                console.error("Erreur commande:", err);
                // Utiliser un toast non bloquant si disponible, sinon log uniquement
                try {
                    if (typeof showToast === 'function') showToast('Une erreur est survenue lors de la création de la commande.');
                    else console.error('Erreur commande (toast non disponible) :', err);
                } catch (e) {
                    console.error('Erreur affichage toast:', e);
                }
            });
    }

    // -------------------------
    // Gestion inscription + connexion automatique persistante
    // -------------------------
    // -----------------------------
    // Quand un client s’inscrit, mise à jour automatique du modal "Moi"
    // -----------------------------
    (function handleInscriptionQueryParam() {
        try {
            const params = new URLSearchParams(window.location.search);
            if (params.get('inscription') === 'ok') {
                const client_id = params.get('client_id');
                if (client_id) {
                    // Création de l'objet utilisateur
                    window.authUser = {
                        id: client_id,
                        nom: params.get('nom') || 'Client',
                        tel: params.get('tel') || '',
                        whatsapp: params.get('whatsapp') || '',
                        adresse: params.get('adresse') || '',
                        statut: 'actif'
                    };

                    // Sauvegarde dans le localStorage
                    localStorage.setItem('authUser', JSON.stringify(window.authUser));

                    // Met à jour directement le modal "Moi"
                    mettreAJourModalMoi();

                    // Masquer le formulaire d’inscription
                    const formInscription = document.getElementById('inscriptionForm');
                    const modalInscription = document.getElementById('inscriptionModal');
                    if (formInscription) formInscription.style.display = 'none';
                    if (modalInscription) {
                        const bsModal = bootstrap.Modal.getInstance(modalInscription);
                        if (bsModal) bsModal.hide();
                    }

                    // Si panier non vide → envoi automatique
                    if (produits.length > 0) {
                        envoyerCommande(client_id);
                    }
                }

                // Nettoyer l’URL
                params.delete('inscription');
                params.delete('client_id');
                params.delete('nom');
                params.delete('tel');
                params.delete('whatsapp');
                params.delete('adresse');
                const baseUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                history.replaceState(null, '', baseUrl);
            }
        } catch (err) {
            console.error('Erreur handleInscriptionQueryParam:', err);
        }
    })();


    // -----------------------------
    // Vue Panier / Commande
    // -----------------------------
    const btnVoir = sideCartList.querySelector('.btn-cart');
    btnVoir.addEventListener('click', function (e) {
        e.preventDefault();
        // Récupérer les infos client à la volée (priorité localStorage.clientInfo)
        let clientInfoHtml = '';
        const rawClientInfo = localStorage.getItem('clientInfo');
        if (rawClientInfo) {
            try {
                const parsed = JSON.parse(rawClientInfo);
                const c = parsed.client;
                clientInfoHtml = `
                    <p><strong>Nom :</strong> ${c.nom}</p>
                    <p><strong>Téléphone :</strong> ${c.tel ?? ''}</p>
                    <p><strong>WhatsApp :</strong> ${c.whatsapp ?? ''}</p>
                    <p><strong>Adresse :</strong> ${c.adresse ?? ''}</p>
                `;
            } catch (e) {
                clientInfoHtml = `<p>Erreur lecture informations client.</p>`;
            }
        } else if (isLoggedIn && user) {
            clientInfoHtml = `
                <p><strong>Nom :</strong> ${user.nom}</p>
                <p><strong>Téléphone :</strong> ${user.tel ?? ''}</p>
                <p><strong>WhatsApp :</strong> ${user.whatsapp ?? ''}</p>
                <p><strong>Adresse :</strong> ${user.adresse ?? ''}</p>
            `;
        } else {
            clientInfoHtml = `<p>Vous n'êtes pas connecté.</p>`;
        }
        document.getElementById('commande-client-info').innerHTML = clientInfoHtml;

        const tbody = document.getElementById('commande-produits');

        function renderTable() {
            tbody.innerHTML = '';
            let totalGeneral = 0;

            produits.forEach((p, index) => {
                totalGeneral += p.total;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${p.nom}</td>
                    <td><input type="number" min="1" value="${p.qty}" class="form-control form-control-sm qty-input" data-index="${index}" style="width:80px;"></td>
                    <td>${new Intl.NumberFormat('fr-FR').format(p.prix)} GNF</td>
                    <td class="total-produit">${new Intl.NumberFormat('fr-FR').format(p.total)} GNF</td>
                    <td><button class="btn btn-danger btn-sm btn-supprimer" data-index="${index}"><i class="fa fa-trash"></i></button></td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('total-general').textContent =
                new Intl.NumberFormat('fr-FR').format(totalGeneral) + ' GNF';

            tbody.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('input', function () {
                    const i = this.getAttribute('data-index');
                    const newQty = parseInt(this.value);
                    if (newQty > 0) {
                        produits[i].qty = newQty;
                        produits[i].total = produits[i].prix * newQty;
                        updateSideCart();
                        const tableRow = document.querySelector(`#produits-table tbody tr[data-prix="${produits[i].prix}"]`);
                        if (tableRow) tableRow.querySelector('.qty').value = newQty;
                        renderTable();
                    }
                });
            });

            tbody.querySelectorAll('.btn-supprimer').forEach(btn => {
                btn.addEventListener('click', function () {
                    const i = this.getAttribute('data-index');
                    const removedProduit = produits.splice(i, 1)[0];
                    updateSideCart();
                    const tableRow = document.querySelector(`#produits-table tbody tr[data-prix="${removedProduit.prix}"]`);
                    if (tableRow) {
                        const chk = tableRow.querySelector('.select-produit');
                        const qtyInput = tableRow.querySelector('.qty');
                        if (chk) chk.checked = false;
                        if (qtyInput) qtyInput.value = 0;
                    }
                    renderTable();
                });
            });
        }

        renderTable();

        function generateFactureHTML() {
            const totalGeneral = produits.reduce((sum, p) => sum + p.total, 0);

            let html = `
            <div style="font-family:century gothique, sans-serif; padding:20px;">
                <div style="text-align:center;">
                    <img src="/images/logo.png" style="width:120px; height:auto; margin-bottom:10px;">
                    <h2>Mourima Enterprise</h2>
                    <p>
                        Adresse: Nongo - Carrefours Morykanteya<br>
                        Email: mourima.enterprise@gmail.com <br>
                        Tel: 623 24 85 67 | 628 27 53 29 | WhatsApp: 623 24 85 67
                    </p>
                </div>
                <hr>
                <h4>Client:</h4>
                ${clientInfoHtml}
                <table border="1" style="width:100%; border-collapse:collapse; margin-top:10px;">
                    <thead>
                        <tr style="background:#28a745; color:white;">
                            <th style="padding:5px;">Produit</th>
                            <th style="padding:5px;">Quantité</th>
                            <th style="padding:5px;">Prix Unitaire</th>
                            <th style="padding:5px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            produits.forEach(p => {
                html += `<tr>
                    <td style="padding:5px;">${p.nom}</td>
                    <td style="padding:5px; text-align:center;">${p.qty}</td>
                    <td style="padding:5px; text-align:right;">${new Intl.NumberFormat('fr-FR').format(p.prix)} GNF</td>
                    <td style="padding:5px; text-align:right;">${new Intl.NumberFormat('fr-FR').format(p.total)} GNF</td>
                </tr>`;
            });
            html += `
                    </tbody>
                </table>
                <h4 style="text-align:right; margin-top:10px;">Total Général: ${new Intl.NumberFormat('fr-FR').format(totalGeneral)} GNF</h4>
                <p style="text-align:center; margin-top:30px;">Merci pour votre confiance !</p>
            </div>`;
            return html;
        }

        // Vue facture
        document.getElementById('btn-vue-facture')?.addEventListener('click', function () {
            const vueWin = window.open('', 'VueFacture', 'width=800,height=600');
            vueWin.document.write(generateFactureHTML());
            vueWin.document.close();
        });

        // PDF
        document.getElementById('btn-download-pdf')?.addEventListener('click', function () {
            if (typeof jsPDF === 'undefined' || !jsPDF.API.autoTable) {
                alert('Veuillez inclure jsPDF et jsPDF autoTable.');
                return;
            }

            const doc = new jsPDF();
            let y = 20;

            const logo = new Image();
            logo.src = '/images/logo.png';

            logo.onload = function () {
                try {
                    // --- En-tête ---
                    doc.addImage(logo, 'PNG', 80, 5, 50, 20);
                    doc.setFontSize(16);
                    doc.text("Mourima Market", 105, 30, { align: "center" });
                    y = 40;

                    doc.setFontSize(10);
                    doc.text("Adresse: Nongo - Carrefours Morykanteya", 105, y, { align: "center" });
                    y += 5;
                    doc.text("Email: mourima.enterprise@gmail.com | Tel: 623 24 85 67", 105, y, { align: "center" });
                    y += 10;

                    // --- Informations client ---
                    doc.setFontSize(12);
                    doc.text("Client :", 10, y);
                    y += 6;

                    // Récupérer les infos client (priorité clientInfo)
                    const rawClientInfoPdf = localStorage.getItem('clientInfo');
                    if (rawClientInfoPdf) {
                        try {
                            const parsed = JSON.parse(rawClientInfoPdf);
                            const c = parsed.client || {};
                            const nom = c.nom || 'Non précisé';
                            const tel = c.tel || 'Non précisé';
                            const whatsapp = c.whatsapp || 'Non précisé';
                            const adresse = c.adresse || 'Non précisée';

                            doc.text(`Nom : ${nom}`, 10, y); y += 5;
                            doc.text(`Téléphone : ${tel}`, 10, y); y += 5;
                            doc.text(`WhatsApp : ${whatsapp}`, 10, y); y += 5;
                            doc.text(`Adresse : ${adresse}`, 10, y);
                        } catch (e) {
                            doc.text("Vous n'êtes pas connecté ou vos informations ne sont pas disponibles.", 10, y);
                        }
                    } else if (typeof isLoggedIn !== 'undefined' && isLoggedIn && user) {
                        const nom = user.nom || 'Non précisé';
                        const tel = user.tel || 'Non précisé';
                        const whatsapp = user.whatsapp || 'Non précisé';
                        const adresse = user.adresse || 'Non précisée';

                        doc.text(`Nom : ${nom}`, 10, y); y += 5;
                        doc.text(`Téléphone : ${tel}`, 10, y); y += 5;
                        doc.text(`WhatsApp : ${whatsapp}`, 10, y); y += 5;
                        doc.text(`Adresse : ${adresse}`, 10, y);
                    } else {
                        doc.text("Vous n'êtes pas connecté ou vos informations ne sont pas disponibles.", 10, y);
                    }
                    y += 10;

                    // --- Tableau des produits ---
                    const headers = [["Produit", "Quantité", "Prix Unitaire", "Total"]];
                    const data = (produits || []).map(p => [
                        p.nom,
                        p.qty,
                        `${new Intl.NumberFormat('fr-FR').format(p.prix)} GNF`,
                        `${new Intl.NumberFormat('fr-FR').format(p.total)} GNF`
                    ]);

                    doc.autoTable({
                        startY: y,
                        head: headers,
                        body: data,
                        theme: 'grid',
                        headStyles: { fillColor: [40, 167, 69], textColor: 255 },
                        styles: { fontSize: 10, cellPadding: 3, overflow: 'linebreak', valign: 'middle' },
                        columnStyles: {
                            0: { cellWidth: 60 },
                            1: { halign: 'center', cellWidth: 25 },
                            2: { halign: 'right', cellWidth: 40 },
                            3: { halign: 'right', cellWidth: 40 }
                        },
                        tableWidth: 'auto',
                        margin: { left: 10, right: 10 }
                    });

                    // --- Total général ---
                    y = doc.lastAutoTable.finalY + 10;
                    const totalGeneral = (produits || []).reduce((sum, p) => sum + (p.total || 0), 0);
                    doc.setFontSize(12);
                    doc.text(`Total Général : ${new Intl.NumberFormat('fr-FR').format(totalGeneral)} GNF`, 200, y, { align: "right" });
                    y += 15;

                    // --- Pied de page ---
                    doc.text("Merci pour votre confiance !", 105, y, { align: "center" });

                    // --- Sauvegarde ---
                    doc.save('facture.pdf');
                } catch (e) {
                    console.error('Erreur génération PDF :', e);
                    alert("Une erreur est survenue lors de la génération du PDF.");
                }
            };

            logo.onerror = function () {
                alert("Impossible de charger le logo. Vérifiez le chemin '/images/logo.png'.");
            };
        });

        // Impression
        document.getElementById('btn-print-facture')?.addEventListener('click', function () {
            const printWin = window.open('', 'PrintFacture');
            printWin.document.write(generateFactureHTML());
            printWin.document.close();
            printWin.print();
        });

        const modal = new bootstrap.Modal(document.getElementById('voirCommandeModal'));
        modal.show();
    });

    // -----------------------------
    // Affichage des infos client dans le panneau "Moi"
    // -----------------------------
    const clientInfoContainer = document.getElementById('client-info-content');

    // Ne pas écraser le rendu côté serveur si le panneau "Moi" contient déjà
    // un bloc `.user-info` (vieux comportement). Conserver le HTML server-side
    // qui inclut le bouton "Modifier mes informations".
    function hasServerRenderedUserInfo(container) {
        try {
            return !!(container && container.querySelector && container.querySelector('.user-info'));
        } catch (e) {
            return false;
        }
    }

    if (window.authUser) {
        if (!hasServerRenderedUserInfo(clientInfoContainer)) {
            // Utiliser la fonction centralisée updateMoiPanel si elle existe
            if (typeof updateMoiPanel === 'function') {
                updateMoiPanel(window.authUser);
            } else {
                // Fallback si updateMoiPanel n'est pas disponible
                clientInfoContainer.innerHTML = `
                <li><strong>Nom :</strong> ${window.authUser.nom}</li>
                <li><strong>Téléphone :</strong> ${window.authUser.tel}</li>
                <li><strong>WhatsApp :</strong> ${window.authUser.whatsapp || '—'}</li>
                <li><strong>Adresse :</strong> ${window.authUser.adresse}</li>
                <li><strong>Statut :</strong> ${window.authUser.statut}</li>
                <li class="mt-3">
                    <a href="/commandes" class="btn btn-sm btn-primary w-100">
                        <i class="fa fa-shopping-cart me-1"></i> Mes commandes
                    </a>
                </li>
                <li class="mt-3">
                    <a href="/clients/${window.authUser.id}/edit" class="btn btn-sm btn-success w-100">
                        <i class="fa fa-edit me-1"></i> Modifier mes infos
                    </a>
                </li>
                <li class="mt-2">
                    <button id="btn-deconnexion" class="btn btn-sm btn-danger w-100">
                        <i class="fa fa-sign-out-alt me-1"></i> Déconnexion
                    </button>
                </li>
            `;
            }
            if (inscriptionModal) inscriptionModal.style.display = 'none';
        } else {
            // Si le HTML serveur existe déjà, on peut mettre à jour uniquement
            // des champs textuels si nécessaire (ex : téléphone, whatsapp, adresse)
            try {
                const phoneEl = clientInfoContainer.querySelector('.info-item i.fa-phone-alt')?.nextSibling;
                // Mais comme la structure varie, au minimum nous laissons le rendu serveur intact.
            } catch (e) {
                // ignore
            }
        }
    } else {
        if (!hasServerRenderedUserInfo(clientInfoContainer)) {
            clientInfoContainer.innerHTML = `<li class="text-center text-muted">Vous n’êtes pas connecté.</li>`;
        }
    }

    document.getElementById('btn-moi').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('side-moi').classList.add('on');
    });
    document.querySelectorAll('#side-moi .close-side').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('side-moi').classList.remove('on');
        });
    });

    // -----------------------------
    // Persistance panier via localStorage
    // -----------------------------
    const savedProduits = JSON.parse(localStorage.getItem('panier_produits') || '[]');
    if (savedProduits.length > 0) {
        produits = savedProduits;
        updateSideCart();

        // Restaurer l'état des checkbox et quantités dans le tableau 'allproduit'
        try {
            const table = document.getElementById('produits-table');
            if (table) {
                const rows = Array.from(table.querySelectorAll('tbody tr'));
                produits.forEach(p => {
                    // Cherche d'abord par id, ensuite fallback par nom/prix
                    let match = null;
                    if (p.id) {
                        match = rows.find(tr => tr.dataset.id && String(tr.dataset.id) === String(p.id));
                    }
                    if (!match) {
                        match = rows.find(tr => {
                            const nameCell = tr.querySelector('td:nth-child(2)');
                            const nom = nameCell ? (nameCell.textContent || '').trim() : '';
                            return (nom && nom.includes(p.nom)) || (tr.dataset.prix && Number(tr.dataset.prix) === Number(p.prix));
                        });
                    }
                    if (match) {
                        const chk = match.querySelector('.select-produit');
                        const qtyInput = match.querySelector('.qty');
                        if (chk) chk.checked = true;
                        if (qtyInput) {
                            qtyInput.value = p.qty || 1;
                            qtyInput.disabled = false;
                        }
                    }
                });

                // Après avoir restauré les cases/qty côté DOM, recalculer produits à partir du tableau
                syncFromTable();
            }
        } catch (e) {
            console.warn('Impossible de restaurer l\'état du tableau produits:', e);
        }
    }

    function savePanier() {
        localStorage.setItem('panier_produits', JSON.stringify(produits));
    }

    const oldUpdateSideCart = updateSideCart;
    updateSideCart = function () {
        oldUpdateSideCart();
        savePanier();
    }

    const oldSyncFromTable = syncFromTable;
    syncFromTable = function () {
        oldSyncFromTable();
        savePanier();
    }

    const oldEnvoyerCommande = envoyerCommande;
    envoyerCommande = function (client_id = null) {
        oldEnvoyerCommande(client_id);
        localStorage.removeItem('panier_produits');
    };

    // -----------------------------
    // Fonction pour mettre à jour le modal "Moi"
    // -----------------------------
    function mettreAJourModalMoi() {
        const clientInfoContainer = document.getElementById('client-info-content');
        const user = window.authUser;

        if (user && clientInfoContainer) {
            // Si le serveur a déjà rendu le bloc `.user-info`, ne pas écraser
            // pour conserver le bouton "Modifier mes informations".
            if (clientInfoContainer.querySelector('.user-info')) {
                return; // laisser le rendu serveur
            }

            // Utiliser la fonction centralisée updateMoiPanel si elle existe
            if (typeof updateMoiPanel === 'function') {
                updateMoiPanel(user);
            } else {
                // Fallback si updateMoiPanel n'est pas disponible
                clientInfoContainer.innerHTML = `
            <li><strong>Nom :</strong> ${user.nom}</li>
            <li><strong>Téléphone :</strong> ${user.tel}</li>
            <li><strong>WhatsApp :</strong> ${user.whatsapp || '—'}</li>
            <li><strong>Adresse :</strong> ${user.adresse}</li>
            <li><strong>Statut :</strong> ${user.statut}</li>
            <li class="mt-3">
                <a href="/commandes" class="btn btn-sm btn-primary w-100">
                    <i class="fa fa-shopping-cart me-1"></i> Mes commandes
                </a>
            </li>
            <li class="mt-3">
                <a href="/clients/${user.id}/edit" class="btn btn-sm btn-success w-100">
                    <i class="fa fa-edit me-1"></i> Modifier mes infos
                </a>
            </li>
            <li class="mt-2">
                <button id="btn-deconnexion" class="btn btn-sm btn-danger w-100">
                    <i class="fa fa-sign-out-alt me-1"></i> Déconnexion
                </button>
            </li>
        `;
            }

            // Gestion du bouton Déconnexion
            const btnDeconnexion = document.getElementById('btn-deconnexion');
            if (btnDeconnexion && !btnDeconnexion.dataset.listenerAttached) {
                btnDeconnexion.dataset.listenerAttached = '1';
                btnDeconnexion.addEventListener('click', () => {
                    localStorage.removeItem('authUser');
                    localStorage.removeItem('clientInfo');
                    window.authUser = null;
                    clientInfoContainer.innerHTML = `<li class="text-center text-muted">Vous n'êtes pas connecté.</li>`;
                    const inscriptionForm = document.getElementById('inscriptionForm');
                    if (inscriptionForm) inscriptionForm.style.display = 'block';
                });
            }
        }
    }

    if (window.authUser) {
        mettreAJourModalMoi();
    }



});

