document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('#inscriptionModal form');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Fermer le modal inscription
                const modal = bootstrap.Modal.getInstance(document.getElementById('inscriptionModal'));
                modal.hide();

                // Afficher le modal récapitulatif des commandes
                showCommandeModal(data.client, data.commande);
            } else {
                alert("Erreur lors de l'inscription.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Erreur serveur.");
        });
    });

    function showCommandeModal(client, commande) {
        let modalHtml = `
            <div class="modal fade" id="commandeModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header" style="background:#1c911e;">
                            <h5 class="modal-title text-white">Bonjour ${client.nom}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Votre commande en cours a été créée avec succès !</p>
                            <p><strong>ID Commande :</strong> ${commande.id}</p>
                            <p><strong>Statut :</strong> ${commande.status}</p>
                            <p>Vous pourrez suivre toutes vos commandes depuis votre espace client.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn" style="background:#1c911e; color:white;" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const commandeModal = new bootstrap.Modal(document.getElementById('commandeModal'));
        commandeModal.show();
    }
});
