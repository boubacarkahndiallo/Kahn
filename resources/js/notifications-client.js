// Client notifications script
// S'abonne aux canaux broadcast si Echo est disponible

function showToast(title, message) {
    // Utiliser les toasts Bootstrap si disponibles
    try {
        const container = document.getElementById('toast-container');
        if (container) {
            const toastEl = document.createElement('div');
            toastEl.className = 'toast';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
                <div class="toast-header">
                    <strong class="me-auto">${title}</strong>
                    <small class="text-muted">Maintenant</small>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${message}</div>
            `;
            container.appendChild(toastEl);
            const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 5000 });
            bsToast.show();
            return;
        }
    } catch (e) {
        // fallback
    }
    if (typeof window.showToast === 'function') {
        window.showToast(message);
        return;
    }
    alert(title + '\n' + message);
}

function initNotifications() {
    if (!window.Echo) {
        console.warn('Laravel Echo non initialisé. Les notifications en temps réel ne fonctionnent pas.');
        return;
    }

    // Channel public produits
    try {
        window.Echo.channel('products')
            .listen('ProductCreated', (e) => {
                const title = 'Nouveau produit';
                const msg = `${e.nom} — ${e.prix} GNF`;
                showToast(title, msg);
                // Optionnel: rafraîchir la liste de produits
            });
    } catch (e) {
        console.warn('Erreur subscription products', e);
    }

    // Channel admin orders (pour admins)
    try {
        window.Echo.private('admin-orders')
            .listen('OrderCreated', (e) => {
                const title = 'Nouvelle commande';
                const msg = `${e.numero} — ${e.prix_total} GNF de ${e.client_nom}`;
                showToast(title, msg);
            });
    } catch (e) {
        console.warn('Erreur subscription admin-orders', e);
    }

    // Channel private user notifications
    if (window.authUser && window.authUser.id) {
        try {
            window.Echo.private(`user.${window.authUser.id}.notifications`)
                .listen('OrderCreated', (e) => {
                    const title = 'Votre commande';
                    const msg = `Commande ${e.numero} enregistrée (${e.prix_total} GNF)`;
                    showToast(title, msg);
                });

            window.Echo.private(`user.${window.authUser.id}.notifications`)
                .listen('ProductCreated', (e) => {
                    const title = 'Nouvel article';
                    const msg = `${e.nom} disponible`;
                    showToast(title, msg);
                });
        } catch (e) {
            console.warn('Erreur subscription user notifications', e);
        }
    }
}

// Auto-init if Echo already loaded
window.addEventListener('load', function () {
    setTimeout(initNotifications, 500);
});

export default initNotifications;
