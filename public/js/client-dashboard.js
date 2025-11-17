// public/js/client-dashboard.js
document.addEventListener('DOMContentLoaded', function () {
    // Récupérer les commandes du client
    function loadClientCommandes() {
        fetch('/api/mes-commandes')
            .then(res => res.json())
            .then(data => {
                const container = document.querySelector('#side-moi .commandes-list');
                if (container && data.commandes) {
                    container.innerHTML = data.commandes.map(cmd => `
                        <div class="commande-item">
                            <div class="d-flex justify-content-between">
                                <strong>${cmd.numero_commande}</strong>
                                <span class="badge ${cmd.statut === 'en_cours' ? 'bg-warning' : (cmd.statut === 'livree' ? 'bg-success' : 'bg-danger')}">
                                    ${cmd.statut}
                                </span>
                            </div>
                            <small class="text-muted">${cmd.date_commande}</small>
                            <p class="mb-1">${cmd.produits_description}</p>
                            <strong class="float-end">${cmd.total_format}</strong>
                        </div>
                    `).join('') || '<p class="text-center text-muted">Aucune commande</p>';
                }
            })
            .catch(console.error);
    }

    // Chargement initial
    loadClientCommandes();

    // Recharger toutes les 5 minutes
    setInterval(loadClientCommandes, 300000);

    // Mise à jour des infos client
    function updateClientInfo() {
        const clientInfo = JSON.parse(localStorage.getItem('clientInfo'));
        if (clientInfo && clientInfo.client) {
            const info = document.querySelector('#side-moi .client-info');
            if (info) {
                info.innerHTML = `
                    <div class="user-details">
                        <h5>${clientInfo.client.nom}</h5>
                        <p class="mb-1"><i class="fa fa-phone"></i> ${clientInfo.client.tel}</p>
                        ${clientInfo.client.whatsapp ? `<p class="mb-1"><i class="fab fa-whatsapp"></i> ${clientInfo.client.whatsapp}</p>` : ''}
                        <p class="mb-1"><i class="fa fa-map-marker"></i> ${clientInfo.client.adresse}</p>
                        <div class="mt-3">
                            <a href="/clients/${clientInfo.client.id}/edit" class="btn btn-sm btn-primary w-100">
                                <i class="fa fa-edit"></i> Modifier mes informations
                            </a>
                        </div>
                    </div>
                `;
            }
        }
    }

    // Mise à jour initiale
    updateClientInfo();

    // Écouter les changements dans le localStorage
    window.addEventListener('storage', function (e) {
        if (e.key === 'clientInfo') {
            updateClientInfo();
        }
    });
});
