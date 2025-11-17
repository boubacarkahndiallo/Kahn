/**
 * Gestion des notifications en temps réel pour les admins
 * Affiche les notifications de commandes reçues
 */

let notificationInterval = null;

function initNotifications() {
    // Vérifier si l'utilisateur est admin ou super_admin
    const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content') || '';

    if (!['admin', 'super_admin'].includes(userRole)) {
        return; // Pas d'admin, pas de notifications
    }

    // Charger les notifications non lues au démarrage
    loadUnreadNotifications();

    // Vérifier les notifications toutes les 5 secondes
    notificationInterval = setInterval(loadUnreadNotifications, 5000);
}

function loadUnreadNotifications() {
    fetch('/api/notifications/unread', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    })
        .then(response => response.json())
        .then(data => {
            if (data.notifications && data.notifications.length > 0) {
                displayNotifications(data.notifications);
                updateNotificationBadge(data.unread_count);
            }
        })
        .catch(error => console.warn('Erreur chargement notifications:', error));
}

function displayNotifications(notifications) {
    const container = document.getElementById('notification-container');

    if (!container) {
        // Créer le conteneur s'il n'existe pas
        const newContainer = document.createElement('div');
        newContainer.id = 'notification-container';
        newContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(newContainer);
    }

    // Afficher les 3 premières notifications
    notifications.slice(0, 3).forEach((notification, index) => {
        const notificationEl = createNotificationElement(notification);
        const targetContainer = document.getElementById('notification-container');
        targetContainer.appendChild(notificationEl);

        // Auto-fermer après 10 secondes
        setTimeout(() => {
            notificationEl.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => notificationEl.remove(), 300);
        }, 10000);

        // Marquer comme lue après 3 secondes
        setTimeout(() => {
            markNotificationAsRead(notification.id);
        }, 3000);
    });
}

function createNotificationElement(notification) {
    const div = document.createElement('div');
    div.className = 'notification-toast';
    div.style.cssText = `
        background: white;
        border-left: 5px solid #1c911e;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideInRight 0.3s ease-out;
        cursor: pointer;
    `;

    const data = notification.data || {};
    const title = notification.title || 'Nouvelle notification';
    const message = notification.message || '';

    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div style="flex: 1;">
                <h6 style="margin: 0 0 5px 0; color: #1c911e; font-weight: bold;">
                    <i class="fa fa-bell" style="margin-right: 8px;"></i>${title}
                </h6>
                <p style="margin: 5px 0; color: #333; font-size: 14px;">
                    ${message}
                </p>
                ${data.numero_commande ? `
                    <small style="color: #666; display: block; margin-top: 8px;">
                        <i class="fa fa-hash"></i> ${data.numero_commande}
                        <span style="margin: 0 5px;">•</span>
                        ${data.client_nom || 'Client'}
                        <span style="margin: 0 5px;">•</span>
                        ${data.prix_total || '0'} Fcfa
                    </small>
                ` : ''}
            </div>
            <button class="notification-close" style="
                background: none;
                border: none;
                color: #999;
                cursor: pointer;
                font-size: 18px;
                padding: 0;
                margin-left: 10px;
            " onclick="this.parentElement.parentElement.remove();">
                <i class="fa fa-times"></i>
            </button>
        </div>
    `;

    // Clique sur notification → rediriger vers la commande
    div.addEventListener('click', (e) => {
        if (e.target.closest('.notification-close')) return;
        if (data.commande_id) {
            window.location.href = `/commandes/${data.commande_id}`;
        }
    });

    return div;
}

function updateNotificationBadge(count) {
    // Mise à jour du badge dans la navbar
    const navbarBadge = document.getElementById('notification-badge-navbar');
    if (navbarBadge) {
        if (count > 0) {
            navbarBadge.textContent = count > 99 ? '99+' : count;
            navbarBadge.style.display = 'inline-flex';
        } else {
            navbarBadge.style.display = 'none';
        }
    }

    // Mise à jour du badge toast (ancien système - gardé pour compatibilité)
    const badge = document.getElementById('notification-badge');

    if (!badge && count > 0) {
        // Créer le badge s'il n'existe pas
        const newBadge = document.createElement('span');
        newBadge.id = 'notification-badge';
        newBadge.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            z-index: 10000;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        `;
        document.body.appendChild(newBadge);
    }

    if (badge) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
} function markNotificationAsRead(notificationId) {
    fetch(`/api/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
        .catch(error => console.warn('Erreur marquage notification:', error));
}

function markAllAsRead() {
    fetch('/api/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
        .then(response => response.json())
        .then(data => {
            loadUnreadNotifications();
        })
        .catch(error => console.warn('Erreur marquage tout lire:', error));
}

// Animations CSS
if (!document.getElementById('notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(400px);
            }
        }

        .notification-toast {
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);
}

// Initialiser les notifications quand le DOM est prêt
document.addEventListener('DOMContentLoaded', initNotifications);

// Nettoyage si la page se ferme
window.addEventListener('beforeunload', () => {
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
});
