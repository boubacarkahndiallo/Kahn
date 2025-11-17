// Script robuste pour la recherche et le tri du tableau des commandes
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableCommandes = document.getElementById('tableCommandes');

    if (!searchInput || !tableCommandes) {
        console.warn('Éléments de recherche/tri non trouvés');
        return;
    }

    // `tableCommandes` peut être le <tbody> (c'est le cas ici). Récupérer le tbody et la table parente.
    const tableBody = (tableCommandes.tagName && tableCommandes.tagName.toLowerCase() === 'tbody')
        ? tableCommandes
        : (tableCommandes.querySelector('tbody') || tableCommandes);
    const tableElement = tableBody.closest('table');
    const tableHeads = tableElement ? tableElement.querySelectorAll('thead th') : document.querySelectorAll('#tableCommandes thead th');

    // Fonction de recherche
    function applySearch() {
        const searchValue = searchInput.value.toLowerCase().trim();
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();

            if (searchValue === '' || text.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        updateRowNumbers();
    }

    // Fonction de tri
    function sortByColumn(columnIndex) {
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        const isNumeric = columnIndex === 0 || columnIndex === 4; // Index et Total

        rows.sort((a, b) => {
            const cellA = a.querySelectorAll('td')[columnIndex]?.textContent.trim() || '';
            const cellB = b.querySelectorAll('td')[columnIndex]?.textContent.trim() || '';

            if (isNumeric) {
                const numA = parseFloat(cellA.replace(/\s/g, '').replace(',', '.')) || 0;
                const numB = parseFloat(cellB.replace(/\s/g, '').replace(',', '.')) || 0;
                return numA - numB;
            }
            return cellA.localeCompare(cellB, 'fr');
        });

        // Remplacer les lignes triées
        tableBody.innerHTML = '';
        rows.forEach(row => tableBody.appendChild(row));
        updateRowNumbers();
    }

    // Mettre à jour les numéros de ligne
    function updateRowNumbers() {
        let index = 1;
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            if (row.style.display !== 'none') {
                row.querySelector('td:first-child').textContent = index;
                index++;
            }
        });
    }

    // Événement de recherche
    searchInput.addEventListener('keyup', applySearch);

    // Bouton d'effacement de recherche
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-clear-search')) {
            searchInput.value = '';
            applySearch();
        }
    });

    // Événements de tri sur les en-têtes
    tableHeads.forEach((th, index) => {
        if (index < tableHeads.length - 1) { // Pas pour la colonne Actions
            th.style.cursor = 'pointer';
            th.style.userSelect = 'none';

            th.addEventListener('mouseenter', function () {
                this.style.backgroundColor = '#168a10';
            });

            th.addEventListener('mouseleave', function () {
                this.style.backgroundColor = '#1c911e';
            });

            th.addEventListener('click', function () {
                sortByColumn(index);
            });
        }
    });

    // Fonction globale pour ajouter une commande au tableau
    window.addCommandeToTable = function (data) {
        const produits = Array.isArray(data.produits) ? data.produits : JSON.parse(data.produits);
        let produitsHtml = '';

        produits.forEach(p => {
            const qty = p.qty || p.quantite || 1;
            const prix = Number(p.prix) || 0;
            const total = prix * qty;
            produitsHtml += `<div class="mb-1">${p.nom} (x${qty}) - ${prix.toLocaleString('fr-FR')} GNF <span class="text-success">= ${total.toLocaleString('fr-FR')} GNF</span></div>`;
        });

        const statutHtml = data.statut === 'en_cours'
            ? '<span class="fw-bold text-warning">En cours</span>'
            : (data.statut === 'livree'
                ? '<span class="fw-bold text-success">Livrée</span>'
                : '<span class="fw-bold text-danger">Annulée</span>');

        const dateFormatted = data.date_commande.split(' ')[0];

        const row = document.createElement('tr');
        row.setAttribute('data-commande-id', data.id);
        row.innerHTML = `
            <td>1</td>
            <td>${data.numero_commande}</td>
            <td>${data.client_nom}</td>
            <td>
                <div class="text-start" style="max-height: 100px; overflow-y: auto;">
                    ${produitsHtml}
                </div>
            </td>
            <td>${Number(data.prix_total).toLocaleString('fr-FR')} GNF</td>
            <td>${statutHtml}</td>
            <td>${dateFormatted}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-voir" style="color:#070a23;" data-id="${data.id}" title="Voir">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-modifier" style="color:#1c911e;" data-id="${data.id}" title="Modifier">
                        <i class="fa fa-edit"></i>
                    </button>
                    <form action="/commandes/${data.id}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm" style="color:red;" onclick="return confirm('Êtes-vous sûr ?')" title="Supprimer">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        `;

        tableBody.insertBefore(row, tableBody.firstChild);
        updateRowNumbers();
        applySearch();
    };

    console.log('Script table-search-sort chargé avec succès');
});
