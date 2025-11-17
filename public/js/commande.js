$(document).ready(function () {
    let index = 1;

    // Sauvegarde des options de produits pour clonage
    const produitsOptions = $('#produitsTableBody select.produitSelect:first').html();

    // Attacher les événements à une ligne
    function attachRowListeners(row) {
        row.find('.produitSelect').on('change', function () {
            const prix = $(this).find(':selected').data('prix') || 0;
            row.find('.prixInput').val(prix);
            calculerTotalProduit(row);
            updatePrixTotal();
        });
        row.find('.quantiteInput').on('input', function () {
            calculerTotalProduit(row);
            updatePrixTotal();
        });
        row.find('.removeRow').on('click', function () {
            row.remove();
            updatePrixTotal();
        });
    }

    // Calcul du total par produit
    function calculerTotalProduit(row) {
        const prix = parseFloat(row.find('.prixInput').val()) || 0;
        const quantite = parseInt(row.find('.quantiteInput').val()) || 0;
        const total = prix * quantite;
        row.find('.totalProduit').text(total.toLocaleString('fr-FR'));
    }

    // Calcul du total général
    function updatePrixTotal() {
        let total = 0;
        $('.totalProduit').each(function () {
            total += parseFloat($(this).text().replace(/\s/g, '')) || 0;
        });
        $('#prix_total').val(total);
    }

    // Ajouter une nouvelle ligne produit
    $('#addProduct').on('click', () => {
        const tbody = $('#produitsTableBody');
        const row = $(`
            <tr>
                <td>
                    <select name="produits[${index}][nom]" class="form-select produitSelect" required>
                        ${produitsOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="produits[${index}][qty]"
                        class="form-control quantiteInput" value="1" min="1" required>
                </td>
                <td>
                    <input type="number" name="produits[${index}][prix]"
                        class="form-control prixInput" readonly>
                </td>
                <td class="totalProduit fw-bold text-success">0</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
        tbody.append(row);
        attachRowListeners(row);
        index++;
        updatePrixTotal();
    });

    // Activer les événements sur la première ligne déjà présente
    $('#produitsTableBody tr').each(function () {
        attachRowListeners($(this));
    });

    // Soumission du formulaire de commande
    $('#commandeForm').on('submit', function (e) {
        e.preventDefault();
        const form = this;

        // Construire le tableau des produits
        const produits = [];
        $('#produitsTableBody tr').each(function () {
            const nom = $(this).find('.produitSelect').val();
            const qty = parseInt($(this).find('.quantiteInput').val()) || 0;
            const prix = parseFloat($(this).find('.prixInput').val()) || 0;
            if (nom) produits.push({ nom, qty, prix });
        });

        if (produits.length === 0) {
            alert('Veuillez ajouter au moins un produit.');
            return;
        }

        // Ajouter champ caché JSON (utilisé par Laravel)
        if ($('#produits_json').length === 0) {
            $(form).append('<input type="hidden" id="produits_json" name="produits">');
        }
        $('#produits_json').val(JSON.stringify(produits));

        const formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    if (window.addCommandeToTable) {
                        window.addCommandeToTable(response.commande);
                    }
                    form.reset();
                    $('#produitsTableBody').html(''); // vide les lignes produits
                    $('#ajoutCommandeModal').modal('hide');
                    index = 1;
                    updatePrixTotal();
                } else {
                    // Utiliser un toast non bloquant si disponible
                    if (typeof showToast === 'function') showToast(response.message || 'Erreur lors de la création de la commande.');
                    else alert(response.message || 'Erreur lors de la création de la commande.');
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                // Try to display validation errors inside the modal if present
                try {
                    var container = $('#commandeErrors');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var messages = [];
                        Object.keys(xhr.responseJSON.errors).forEach(function (k) {
                            messages = messages.concat(xhr.responseJSON.errors[k]);
                        });
                        container.html(messages.map(m => `<div>${m}</div>`).join(''));
                        container.removeClass('d-none');
                        return;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        container.html(`<div>${xhr.responseJSON.message}</div>`).removeClass('d-none');
                        return;
                    }
                } catch (e) {
                    console.error('Erreur affichage validation:', e);
                }
                if (typeof showToast === 'function') showToast('Erreur : veuillez vérifier les champs.');
                else alert('Erreur : veuillez vérifier les champs.');
            }
        });
    });
});
