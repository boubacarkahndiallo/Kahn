document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById('produits-table');
    const grandTotal = document.getElementById('grand-total');
    const btnCommander = document.getElementById('btn-commander');
    let produitsSelectionnes = [];

    function updateProduitsSelectionnes() {
        produitsSelectionnes = [];
        table.querySelectorAll('tbody tr').forEach(tr => {
            const chk = tr.querySelector('.select-produit');
            if (chk.checked) {
                const nom = tr.querySelector('td:nth-child(2) span').textContent.trim();
                const qty = parseInt(tr.querySelector('.qty').value) || 1;
                const prix = parseFloat(tr.dataset.prix);
                const total = prix * qty;
                const img = tr.querySelector('td:nth-child(2) img');
                const image = img ? img.src : 'https://via.placeholder.com/60';
                produitsSelectionnes.push({ nom, qty, prix, total, image });
            }
        });
    }

    function updateTotal(tr) {
        const prix = parseFloat(tr.dataset.prix);
        const qty = parseInt(tr.querySelector('.qty').value) || 0;
        tr.querySelector('.total').textContent = new Intl.NumberFormat('fr-FR').format(prix * qty);
    }

    function updateGrandTotal() {
        let total = 0;
        table.querySelectorAll('tbody tr').forEach(tr => {
            const chk = tr.querySelector('.select-produit');
            if (chk.checked) {
                const prix = parseFloat(tr.dataset.prix);
                const qty = parseInt(tr.querySelector('.qty').value) || 0;
                total += prix * qty;
            }
        });
        grandTotal.textContent = new Intl.NumberFormat('fr-FR').format(total);
    }

    function toggleCommanderButton() {
        const anyChecked = Array.from(table.querySelectorAll('.select-produit')).some(chk => chk.checked);
        btnCommander.classList.toggle('d-none', !anyChecked);
    }

    // Fonctions pour activer/désactiver les sélections (utilisables globalement)
    function disableAllSelections() {
        // Désactiver toutes les cases à cocher
        table.querySelectorAll('.select-produit').forEach(checkbox => {
            checkbox.disabled = true;
            checkbox.checked = false;
            const tr = checkbox.closest('tr');
            const qtyInput = tr.querySelector('.qty');
            if (qtyInput) {
                qtyInput.disabled = true;
                qtyInput.value = 1;
            }
            const btnAnnuler = tr.querySelector('.btn-annuler');
            if (btnAnnuler) btnAnnuler.classList.add('d-none');
        });

        const selectAllEl = document.getElementById('select-all');
        if (selectAllEl) {
            selectAllEl.disabled = true;
            selectAllEl.checked = false;
        }

        if (grandTotal) grandTotal.textContent = '0';
        if (btnCommander) btnCommander.classList.add('d-none');
    }

    function enableAllSelections() {
        table.querySelectorAll('.select-produit').forEach(checkbox => {
            checkbox.disabled = false;
        });
        const selectAllEl = document.getElementById('select-all');
        if (selectAllEl) selectAllEl.disabled = false;
        if (btnCommander) btnCommander.classList.remove('d-none');
    }

    // Vérifier si l'utilisateur est connecté
    const clientInfo = localStorage.getItem('clientInfo');
    // Utiliser aussi le formulaire situé en haut de la page (si l'utilisateur a commencé à renseigner ses infos)
    const clientForm = document.getElementById('clientRegistrationForm');
    let formFilled = false;
    if (clientForm) {
        const nomInput = clientForm.querySelector('#nom') || document.getElementById('nom');
        const telInput = clientForm.querySelector('input[name="tel"]') || document.getElementById('tel');
        const adresseInput = clientForm.querySelector('#adresse') || document.getElementById('adresse');
        const nomVal = nomInput ? nomInput.value.trim() : '';
        const telVal = telInput ? telInput.value.trim() : '';
        const adresseVal = adresseInput ? adresseInput.value.trim() : '';
        // Considérer le formulaire "rempli" si au moins le nom et le téléphone sont renseignés
        formFilled = (nomVal !== '' && telVal !== '');
    }

    const isLoggedIn = clientInfo !== null || (typeof window.authUser !== 'undefined' && window.authUser !== null) || formFilled;


    // Écouter l'événement personnalisé disparé lors de la connexion/déconnexion
    window.addEventListener('clientInfoChanged', function (e) {
        // e.detail == null => déconnexion; sinon connexion
        if (!e.detail) {
            // déconnexion : désactiver tout
            disableAllSelections();
            // si vous voulez forcer un reload : window.location.reload();
        } else {
            // connexion : activer les sélections
            if (document.querySelector('.alert.alert-warning')) {
                document.querySelectorAll('.alert.alert-warning').forEach(a => a.remove());
            }
            enableAllSelections();
        }
    });

    // Gestion des cases à cocher (pour les utilisateurs connectés uniquement)
    table.querySelectorAll('.select-produit').forEach(chk => {
        chk.addEventListener('change', function () {
            const tr = this.closest('tr');
            const qtyInput = tr.querySelector('.qty');
            const btnAnnuler = tr.querySelector('.btn-annuler');
            qtyInput.disabled = !this.checked;
            if (this.checked) btnAnnuler.classList.remove('d-none');
            else {
                btnAnnuler.classList.add('d-none');
                qtyInput.value = 1;
            }
            updateProduitsSelectionnes();
            updateTotal(tr);
            updateGrandTotal();
            toggleCommanderButton();
        });
    });

    // Gestion des quantités
    table.querySelectorAll('.qty').forEach(input => {
        input.addEventListener('input', function () {
            const tr = this.closest('tr');
            updateTotal(tr);
            updateGrandTotal();
            updateProduitsSelectionnes();
        });
    });

    // Gestion des boutons d'annulation
    table.querySelectorAll('.btn-annuler').forEach(btn => {
        btn.addEventListener('click', function () {
            const tr = this.closest('tr');
            const chk = tr.querySelector('.select-produit');
            chk.checked = false;
            tr.querySelector('.qty').value = 1;
            tr.querySelector('.qty').disabled = true;
            this.classList.add('d-none');
            updateProduitsSelectionnes();
            updateTotal(tr);
            updateGrandTotal();
            toggleCommanderButton();
        });
    });

    // Gestion du "Tout sélectionner"
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const checked = this.checked;
            table.querySelectorAll('.select-produit').forEach(chk => {
                chk.checked = checked;
                chk.dispatchEvent(new Event('change'));
            });
        });
    }

    // Filtrage
    const filterCategorie = document.getElementById('categorie-filter');
    const searchInput = document.getElementById('search-produit');

    function applyFilters() {
        const catVal = filterCategorie.value.toLowerCase();
        const searchVal = searchInput.value.toLowerCase();

        table.querySelectorAll('tbody tr').forEach(tr => {
            const cat = tr.dataset.categorie.toLowerCase();
            const nom = tr.querySelector('td:nth-child(2) span').textContent.toLowerCase();

            if ((catVal === "" || cat === catVal) && nom.includes(searchVal)) {
                tr.classList.remove('d-none');
            } else {
                tr.classList.add('d-none');
            }
        });
    }

    if (filterCategorie && searchInput) {
        filterCategorie.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);
    }
});
