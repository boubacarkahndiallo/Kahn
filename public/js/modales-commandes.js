// Script pour gérer les modales Voir et Modifier des commandes
$(document).ready(function () {
    const modalVoir = new bootstrap.Modal(document.getElementById('voirCommandeModal'));
    const modalModifier = new bootstrap.Modal(document.getElementById('modifierCommandeModal'));

    // Bouton "Voir" - affiche les détails dans une modale
    $(document).on('click', '.btn-voir', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.ajax({
            url: `/commandes/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const commande = response.commande;

                    // Remplir les infos
                    $('#voir-numero').text(commande.numero_commande);
                    $('#voir-client').text(commande.client_nom);
                    $('#voir-date').text(commande.date_commande);

                    const statusText = commande.statut === 'en_cours'
                        ? 'En cours'
                        : (commande.statut === 'livree' ? 'Livrée' : 'Annulée');
                    $('#voir-statut').text(statusText);

                    // Remplir les produits
                    let produitsHtml = '';
                    if (Array.isArray(commande.produits)) {
                        commande.produits.forEach(p => {
                            const qty = p.qty || p.quantite || 1;
                            const prix = Number(p.prix) || 0;
                            const total = qty * prix;
                            produitsHtml += `<tr>
                                <td>${p.nom}</td>
                                <td>${qty}</td>
                                <td>${prix.toLocaleString('fr-FR')} GNF</td>
                                <td>${total.toLocaleString('fr-FR')} GNF</td>
                            </tr>`;
                        });
                    }
                    $('#voir-produits').html(produitsHtml);
                    $('#voir-total').text(Number(commande.prix_total).toLocaleString('fr-FR') + ' GNF');

                    modalVoir.show();
                }
            },
            error: function (xhr) {
                console.error('Erreur:', xhr);
                alert('Erreur lors du chargement des détails');
            }
        });
    });

    // Bouton "Modifier" - affiche les détails pour modification
    $(document).on('click', '.btn-modifier', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        $.ajax({
            url: `/commandes/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const commande = response.commande;

                    // Remplir le formulaire
                    $('#edit-commande-id').val(commande.id);
                    $('#edit-client').val(commande.client_nom);
                    $('#edit-client-id').val(commande.client_id);
                    $('#edit-statut').val(commande.statut);

                    // Remplir les produits (readonly)
                    let produitsHtml = '';
                    if (Array.isArray(commande.produits)) {
                        commande.produits.forEach(p => {
                            const qty = p.qty || p.quantite || 1;
                            const prix = Number(p.prix) || 0;
                            const total = qty * prix;
                            produitsHtml += `<tr>
                                <td>${p.nom}</td>
                                <td>${qty}</td>
                                <td>${prix.toLocaleString('fr-FR')} GNF</td>
                                <td>${total.toLocaleString('fr-FR')} GNF</td>
                            </tr>`;
                        });
                    }
                    $('#edit-produits').html(produitsHtml);
                    $('#edit-total').text(Number(commande.prix_total).toLocaleString('fr-FR') + ' GNF');

                    modalModifier.show();
                }
            },
            error: function (xhr) {
                console.error('Erreur:', xhr);
                alert('Erreur lors du chargement de la commande');
            }
        });
    });

    // Soumettre le formulaire de modification
    $('#modifierCommandeForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit-commande-id').val();
        const statut = $('#edit-statut').val();

        $.ajax({
            url: `/commandes/${id}`,
            method: 'PUT',
            dataType: 'json',
            data: {
                statut: statut,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    alert('Commande mise à jour avec succès !');
                    modalModifier.hide();
                    location.reload();
                }
            },
            error: function (xhr) {
                console.error('Erreur:', xhr);
                alert('Erreur lors de la mise à jour');
            }
        });
    });
});
