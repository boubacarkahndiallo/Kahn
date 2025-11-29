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
        if (btnCommander) {
            btnCommander.disabled = !anyChecked;
            btnCommander.classList.toggle('pulsing', anyChecked);
            btnCommander.classList.toggle('btn-disabled', !anyChecked);
            btnCommander.setAttribute('aria-disabled', (!anyChecked).toString());
        }
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
        if (btnCommander) {
            btnCommander.disabled = true;
            btnCommander.classList.remove('pulsing');
        }
    }

    function enableAllSelections() {
        table.querySelectorAll('.select-produit').forEach(checkbox => {
            checkbox.disabled = false;
        });
        const selectAllEl = document.getElementById('select-all');
        if (selectAllEl) selectAllEl.disabled = false;
        if (btnCommander) btnCommander.disabled = false;
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
            // Sync grid card if exists
            const id = tr.dataset.id;
            const card = document.querySelector(`.product-card[data-id="${id}"]`);
            if (card) {
                const gridChk = card.querySelector('.grid-select');
                if (gridChk) gridChk.checked = this.checked;
                const gridQty = card.querySelector('.grid-qty');
                if (gridQty) { gridQty.disabled = !this.checked; if (!this.checked) gridQty.value = 1; }
                const gridCancel = card.querySelector('.grid-cancel');
                if (gridCancel) { gridCancel.classList.toggle('d-none', !this.checked); }
            }
        });
    });

    // Gestion des quantités
    table.querySelectorAll('.qty').forEach(input => {
        input.addEventListener('input', function () {
            const tr = this.closest('tr');
            updateTotal(tr);
            updateGrandTotal();
            updateProduitsSelectionnes();
            // sync grid qty if present
            const id = tr.dataset.id;
            const card = document.querySelector(`.product-card[data-id="${id}"]`);
            if (card) {
                const gridQty = card.querySelector('.grid-qty');
                if (gridQty) gridQty.value = this.value;
            }
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
            // sync grid
            const id = tr.dataset.id;
            const card = document.querySelector(`.product-card[data-id="${id}"]`);
            if (card) {
                const gridChk = card.querySelector('.grid-select');
                if (gridChk) gridChk.checked = false;
                const gridQty = card.querySelector('.grid-qty');
                if (gridQty) { gridQty.value = 1; gridQty.disabled = true; }
                const gridCancel = card.querySelector('.grid-cancel');
                if (gridCancel) gridCancel.classList.add('d-none');
            }
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
    const viewListBtn = document.getElementById('view-list');
    const viewGridBtn = document.getElementById('view-grid');
    const viewModeLabel = document.getElementById('view-mode-label');
    const productsGrid = document.getElementById('products-grid');

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

    // Build grid view from table rows
    function createGridFromTable() {
        if (!productsGrid) return;
        productsGrid.innerHTML = '';
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(tr => {
            if (tr.classList.contains('d-none')) return; // respect filters
            const id = tr.dataset.id;
            const prix = tr.dataset.prix;
            const nom = tr.querySelector('td:nth-child(2) .fw-bold') ? tr.querySelector('td:nth-child(2) .fw-bold').textContent.trim() : (tr.querySelector('td:nth-child(2) span') ? tr.querySelector('td:nth-child(2) span').textContent.trim() : 'Produit');
            const img = tr.querySelector('td:nth-child(2) img');
            const imgSrc = img ? img.src : 'https://via.placeholder.com/72';
            const checked = tr.querySelector('.select-produit').checked;
            const qty = tr.querySelector('.qty').value;

            const col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-md-4';

            const card = document.createElement('div');
            card.className = 'product-card';
            card.dataset.id = id;

            card.innerHTML = `
                <input type="checkbox" class="form-check-input grid-select" aria-label="Sélectionner ${nom}" ${checked ? 'checked' : ''} />
                <img src="${imgSrc}" alt="${nom}" class="product-thumbnail" />
                <div class="product-info">
                    <div class="product-name">${nom}</div>
                    <div class="product-price">${new Intl.NumberFormat('fr-FR').format(prix)} GNF</div>
                </div>
                <div class="product-actions">
                    <input type="number" class="form-control form-control-sm grid-qty" min="1" value="${qty}" style="width:80px;" ${checked ? '' : 'disabled'} />
                    <button class="btn btn-danger btn-sm grid-cancel ${checked ? '' : 'd-none'}" title="Annuler">&times;</button>
                </div>
            `;

            col.appendChild(card);
            productsGrid.appendChild(col);
        });

        // Wire grid handlers to sync with table rows
        productsGrid.querySelectorAll('.grid-select').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const card = this.closest('.product-card');
                const id = card.dataset.id;
                const tr = table.querySelector(`tbody tr[data-id="${id}"]`);
                if (!tr) return;
                const tableCheckbox = tr.querySelector('.select-produit');
                tableCheckbox.checked = this.checked;
                tableCheckbox.dispatchEvent(new Event('change'));
                // enable/disable qty on grid
                const gridQty = card.querySelector('.grid-qty');
                const gridCancel = card.querySelector('.grid-cancel');
                if (this.checked) {
                    gridQty.disabled = false;
                    gridCancel.classList.remove('d-none');
                } else {
                    gridQty.disabled = true;
                    gridQty.value = 1;
                    gridCancel.classList.add('d-none');
                }
            });
        });

        productsGrid.querySelectorAll('.grid-qty').forEach(input => {
            input.addEventListener('input', function () {
                const card = this.closest('.product-card');
                const id = card.dataset.id;
                const tr = table.querySelector(`tbody tr[data-id="${id}"]`);
                if (!tr) return;
                const tableQty = tr.querySelector('.qty');
                tableQty.value = this.value;
                tableQty.dispatchEvent(new Event('input'));
            });
        });

        productsGrid.querySelectorAll('.grid-cancel').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.product-card');
                const id = card.dataset.id;
                const tr = table.querySelector(`tbody tr[data-id="${id}"]`);
                if (!tr) return;
                const tableCheckbox = tr.querySelector('.select-produit');
                tableCheckbox.checked = false;
                tableCheckbox.dispatchEvent(new Event('change'));
            });
        });
    }

    function updateViewLabel(mode) {
        if (!viewModeLabel) return;
        viewModeLabel.textContent = `Affichage : ${mode}`;
    }

    function setViewMode(mode) {
        if (mode === 'Grille') {
            // build grid and show
            createGridFromTable();
            // animate: fade out table, fade in grid
            const tableWrapper = document.querySelector('.table-main');
            if (tableWrapper) {
                tableWrapper.classList.add('fade');
                tableWrapper.classList.remove('show');
                setTimeout(() => {
                    tableWrapper.classList.add('d-none');
                    tableWrapper.classList.remove('fade');
                    tableWrapper.classList.remove('show');
                }, 320);
            }
            if (productsGrid) {
                productsGrid.classList.remove('d-none');
                productsGrid.classList.add('fade');
                // small timeout to trigger transition
                setTimeout(() => productsGrid.classList.add('show'), 16);
                productsGrid.setAttribute('aria-hidden', 'false');
            }
            updateViewLabel('Grille');
            if (viewGridBtn) { viewGridBtn.classList.add('active'); viewGridBtn.setAttribute('aria-pressed', 'true'); }
            if (viewListBtn) { viewListBtn.classList.remove('active'); viewListBtn.setAttribute('aria-pressed', 'false'); }
            localStorage.setItem('viewMode', 'Grille');
        } else {
            // animate: fade out grid, fade in table
            if (productsGrid) {
                productsGrid.classList.add('fade');
                productsGrid.classList.remove('show');
                setTimeout(() => {
                    productsGrid.classList.add('d-none');
                    productsGrid.classList.remove('fade');
                    productsGrid.classList.remove('show');
                    productsGrid.setAttribute('aria-hidden', 'true');
                }, 320);
            }
            const tableWrapper = document.querySelector('.table-main');
            if (tableWrapper) {
                tableWrapper.classList.remove('d-none');
                tableWrapper.classList.add('fade');
                setTimeout(() => tableWrapper.classList.add('show'), 16);
            }
            updateViewLabel('Liste');
            if (viewListBtn) { viewListBtn.classList.add('active'); viewListBtn.setAttribute('aria-pressed', 'true'); }
            if (viewGridBtn) { viewGridBtn.classList.remove('active'); viewGridBtn.setAttribute('aria-pressed', 'false'); }
            localStorage.setItem('viewMode', 'Liste');
        }
    }

    if (viewListBtn) viewListBtn.addEventListener('click', function () { setViewMode('Liste'); });
    if (viewGridBtn) viewGridBtn.addEventListener('click', function () { setViewMode('Grille'); });

    // Ensure we rebuild the grid when filters change
    if (filterCategorie) filterCategorie.addEventListener('change', function () {
        if (!productsGrid) return;
        if (!productsGrid.classList.contains('d-none')) createGridFromTable();
    });
    if (searchInput) searchInput.addEventListener('input', function () {
        if (!productsGrid) return;
        if (!productsGrid.classList.contains('d-none')) createGridFromTable();
    });

    // Respect saved view mode
    const savedMode = localStorage.getItem('viewMode') || 'Liste';
    setViewMode(savedMode);
});
