// public/js/panier.js
document.addEventListener("DOMContentLoaded", function () {

    const { jsPDF } = window.jspdf; // pour le lien du pdf dans le panier
    const sideCartList = document.querySelector('.side .cart-list');
    const cartCount = document.getElementById('cart-count');
    const inscriptionForm = document.getElementById('inscriptionForm'); // formulaire
    const inscriptionModal = document.getElementById('inscriptionModal'); // modal
    let produits = [];

    // -----------------------------
    // Gestion utilisateur connecté persistante
    // -----------------------------
    const savedClient = JSON.parse(localStorage.getItem('clientInfo'));
    if (savedClient) {
        window.authUser = savedClient;
    }

    const isLoggedIn = window.authUser !== null;
    const user = window.authUser;

    function updateSideCart() {
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

        sideCartList.querySelector('.total .float-right').innerHTML =
            `<strong>Total</strong>: ${new Intl.NumberFormat('fr-FR').format(produits.reduce((sum, p) => sum + p.total, 0))} GNF`;

        cartCount.textContent = produits.length;

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
                produits.push({ nom, qty, prix, total, image });
            }
        });
        updateSideCart();
    }

    document.querySelectorAll('#produits-table .select-produit').forEach(chk => {
        chk.addEventListener('change', syncFromTable);
    });
    document.querySelectorAll('#produits-table .qty').forEach(input => {
        input.addEventListener('input', syncFromTable);
    });

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
            alert("⚠️ Veuillez sélectionner au moins un produit avant de commander.");
            return;
        }

        const clientInfo = localStorage.getItem('clientInfo');
        if (!clientInfo) {
            alert("⚠️ Veuillez d'abord renseigner vos informations client avant de passer la commande !");
            document.getElementById('client-form').scrollIntoView({ behavior: "smooth" });
            return;
        }

        let message = "🧾 Votre commande :\n";
        let totalGeneral = 0;
        produits.forEach(p => {
            message += `${p.nom} : ${p.qty} x ${new Intl.NumberFormat('fr-FR').format(p.prix)} GNF = ${new Intl.NumberFormat('fr-FR').format(p.total)} GNF\n`;
            totalGeneral += p.total;
        });
        message += `\nTotal à payer : ${new Intl.NumberFormat('fr-FR').format(totalGeneral)} GNF\n\nConfirmez-vous la commande ?`;

        if (confirm(message)) {
            envoyerCommande(window.authUser.id);
        }
    });

    function envoyerCommande(client_id = null) {
        const formData = new FormData();
        formData.append('produits', JSON.stringify(produits));
        formData.append('prix_total', produits.reduce((sum, p) => sum + p.total, 0));
        if (client_id) formData.append('client_id', client_id);

        fetch("/commandes", {
            method: "POST",
            body: formData,
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Commande enregistrée avec succès !");
                    produits = [];
                    updateSideCart();
                } else {
                    alert("Erreur lors de l’enregistrement de la commande.");
                }
            })
            .catch(err => console.error("Erreur commande:", err));
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
                    const clientInfo = {
                        id: client_id,
                        nom: params.get('nom') || 'Client',
                        tel: params.get('tel') || '',
                        whatsapp: params.get('whatsapp') || '',
                        adresse: params.get('adresse') || '',
                        statut: 'actif'
                    };

                    window.authUser = clientInfo;

                    // Sauvegarde dans le localStorage
                    localStorage.setItem('clientInfo', JSON.stringify(clientInfo));

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
        let clientInfoHtml = '';
        if (isLoggedIn) {
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
                    doc.text("Mourima Enterprise", 105, 30, { align: "center" });
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

                    if (typeof isLoggedIn !== 'undefined' && isLoggedIn && user) {
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

    if (window.authUser) {
        clientInfoContainer.innerHTML = `
            <li><strong>Nom :</strong> ${window.authUser.nom}</li>
            <li><strong>Téléphone :</strong> ${window.authUser.tel}</li>
            <li><strong>WhatsApp :</strong> ${window.authUser.whatsapp ?? '—'}</li>
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
                <form method="POST" action="/logout">
                    @csrf
                    <button class="btn btn-sm btn-danger w-100"><i class="fa fa-sign-out-alt me-1"></i> Déconnexion</button>
                </form>
            </li>
        `;
        if (inscriptionModal) inscriptionModal.style.display = 'none';
    } else {
        clientInfoContainer.innerHTML = `<li class="text-center text-muted">Vous n’êtes pas connecté.</li>`;
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

            // Gestion du bouton Déconnexion
            document.getElementById('btn-deconnexion').addEventListener('click', () => {
                localStorage.removeItem('authUser');
                window.authUser = null;
                clientInfoContainer.innerHTML = `<li class="text-center text-muted">Vous n’êtes pas connecté.</li>`;
                const inscriptionForm = document.getElementById('inscriptionForm');
                if (inscriptionForm) inscriptionForm.style.display = 'block';
            });
        }
    }

    if (window.authUser) {
        mettreAJourModalMoi();
    }

});
