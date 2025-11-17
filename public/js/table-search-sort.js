// Script de gestion de la recherche et du tri du tableau des commandes
$(document).ready(function () {
    const searchInput = $('#searchInput');
    const tableCommandes = $('#tableCommandes');
    const tableBody = tableCommandes.find('tbody');
    const tableHeads = tableCommandes.find('thead th');

    // Fonction de recherche
    function applySearch() {
        const searchValue = searchInput.val().toLowerCase().trim();

        tableBody.find('tr').each(function () {
            const row = $(this);
            const text = row.text().toLowerCase();

            if (searchValue === '' || text.includes(searchValue)) {
                row.show();
            } else {
                row.hide();
            }
        });

        updateRowNumbers();
    }

    // Fonction de tri
    function sortByColumn(columnIndex) {
        const rows = tableBody.find('tr').get();
        const isNumeric = columnIndex === 0 || columnIndex === 4; // Index et Total

        rows.sort((a, b) => {
            const cellA = $(a).find(`td:eq(${columnIndex})`).text().trim();
            const cellB = $(b).find(`td:eq(${columnIndex})`).text().trim();

            if (isNumeric) {
                return parseFloat(cellA) - parseFloat(cellB);
            }
            return cellA.localeCompare(cellB, 'fr');
        });

        tableBody.html('');
        rows.forEach(row => tableBody.append(row));
        updateRowNumbers();
    }

    // Mettre à jour les numéros de ligne
    function updateRowNumbers() {
        let index = 1;
        tableBody.find('tr:visible').each(function () {
            $(this).find('td:first').text(index);
            index++;
        });
    }

    // Événement de recherche
    searchInput.on('keyup', function () {
        applySearch();
    });

    // Bouton d'effacement de recherche
    $(document).on('click', '.btn-clear-search', function () {
        searchInput.val('').trigger('keyup');
    });

    // Événements de tri sur les en-têtes
    tableHeads.each(function (index) {
        if (index < tableHeads.length - 1) { // Pas pour la colonne Actions
            $(this).css('cursor', 'pointer').css('user-select', 'none');

            $(this).hover(
                function () { $(this).css('background-color', '#168a10'); },
                function () { $(this).css('background-color', '#1c911e'); }
            );

            $(this).on('click', function () {
                sortByColumn(index);
            });
        }
    });

    // Ajouter une commande au tableau (function globale)
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

        const row = `
            <tr data-commande-id="${data.id}">
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
            </tr>
        `;

        tableBody.prepend(row);
        updateRowNumbers();
        applySearch();
    };
});
