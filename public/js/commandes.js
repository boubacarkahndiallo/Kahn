// public/js/commandes.js - Gestion des modales Voir et Modifier pour les commandes
document.addEventListener('DOMContentLoaded', function () {
    const voirModalElement = document.getElementById('voirCommandeModal');
    const modifierModalElement = document.getElementById('modifierCommandeModal');
    const modalVoir = voirModalElement ? new bootstrap.Modal(voirModalElement) : null;
    const modalModifier = modifierModalElement ? new bootstrap.Modal(modifierModalElement) : null;

    // Voir une commande (délégation d'événements pour supporter les éléments dynamiques)
    document.addEventListener('click', function (e) {
        const btnVoir = e.target.closest('.btn-voir');
        if (btnVoir) {
            e.preventDefault();
            const id = btnVoir.dataset.id;
            fetch(`/commandes/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const commande = data.commande;
                        document.getElementById('voir-numero').textContent = commande.numero_commande;
                        document.getElementById('voir-client').textContent = commande.client_nom;
                        document.getElementById('voir-date').textContent = commande.date_commande;

                        const statutText = commande.statut === 'en_cours'
                            ? 'En cours'
                            : (commande.statut === 'livree' ? 'Livrée' : 'Annulée');
                        document.getElementById('voir-statut').textContent = statutText;

                        // Remplir les produits
                        const produitsHtml = Array.isArray(commande.produits)
                            ? commande.produits.map(p => {
                                const qty = p.qty || p.quantite || 1;
                                const prix = Number(p.prix) || 0;
                                return `<tr>
                                    <td>${p.nom}</td>
                                    <td>${qty}</td>
                                    <td>${prix.toLocaleString('fr-FR')} GNF</td>
                                    <td>${(qty * prix).toLocaleString('fr-FR')} GNF</td>
                                </tr>`;
                            }).join('')
                            : '';

                        document.getElementById('voir-produits').innerHTML = produitsHtml;
                        document.getElementById('voir-total').textContent = Number(commande.prix_total).toLocaleString('fr-FR') + ' GNF';

                        if (modalVoir) modalVoir.show();
                    }
                })
                .catch(err => console.error('Erreur voir commande:', err));
        }
    });

    // Modifier une commande (délégation d'événements)
    document.addEventListener('click', function (e) {
        const btnModifier = e.target.closest('.btn-modifier');
        if (btnModifier) {
            e.preventDefault();
            const id = btnModifier.dataset.id;
            fetch(`/commandes/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const commande = data.commande;
                        document.getElementById('edit-commande-id').value = commande.id;
                        document.getElementById('edit-client').value = commande.client_nom;
                        document.getElementById('edit-client-id').value = commande.client_id;
                        document.getElementById('edit-statut').value = commande.statut;

                        // Remplir les produits
                        const produitsHtml = Array.isArray(commande.produits)
                            ? commande.produits.map(p => {
                                const qty = p.qty || p.quantite || 1;
                                const prix = Number(p.prix) || 0;
                                return `<tr>
                                    <td>${p.nom}</td>
                                    <td>${qty}</td>
                                    <td>${prix.toLocaleString('fr-FR')} GNF</td>
                                    <td>${(qty * prix).toLocaleString('fr-FR')} GNF</td>
                                </tr>`;
                            }).join('')
                            : '';

                        document.getElementById('edit-produits').innerHTML = produitsHtml;
                        document.getElementById('edit-total').textContent = Number(commande.prix_total).toLocaleString('fr-FR') + ' GNF';

                        if (modalModifier) modalModifier.show();
                    }
                })
                .catch(err => console.error('Erreur modifier commande:', err));
        }
    });
});
