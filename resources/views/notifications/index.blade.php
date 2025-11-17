@extends('base')

@section('title', 'Notifications')

@section('content')
    <div class="container-fluid py-4"
        style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh;">
        <div class="container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="mb-0" style="color: #1c911e; font-weight: 600;">
                                <i class="fa fa-bell" style="margin-right: 12px;"></i>Centre de Notifications
                            </h1>
                            <p class="text-muted mt-1">Gérez vos notifications importantes</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button id="markAllRead" class="btn btn-success" onclick="markAllAsRead()"
                                style="border-radius: 8px; box-shadow: 0 2px 8px rgba(28, 145, 30, 0.2);">
                                <i class="fa fa-check-double"></i> Marquer tout comme lu
                            </button>
                        </div>
                    </div>

                    <!-- Filter Bar -->
                    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" id="searchFilter" class="form-control"
                                        placeholder="🔍 Chercher par client..."
                                        style="border-radius: 8px; border: 1px solid #e0e0e0;">
                                </div>
                                <div class="col-md-4">
                                    <select id="statusFilter" class="form-select"
                                        style="border-radius: 8px; border: 1px solid #e0e0e0;">
                                        <option value="all">Tous les statuts</option>
                                        <option value="unread">Non lues</option>
                                        <option value="read">Lues</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="sortFilter" class="form-select"
                                        style="border-radius: 8px; border: 1px solid #e0e0e0;">
                                        <option value="newest">Les plus récentes</option>
                                        <option value="oldest">Les plus anciennes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div
                                    style="width: 50px; height: 50px; background: rgba(28, 145, 30, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-bell" style="font-size: 24px; color: #1c911e;"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted small">Notifications Non Lues</p>
                                    <h3 class="mb-0" id="unreadCount" style="color: #1c911e; font-weight: 600;">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div
                                    style="width: 50px; height: 50px; background: rgba(52, 152, 219, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-inbox" style="font-size: 24px; color: #3498db;"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted small">Total des Commandes</p>
                                    <h3 class="mb-0" id="totalCount" style="color: #3498db; font-weight: 600;">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div
                                    style="width: 50px; height: 50px; background: rgba(46, 204, 113, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-check-circle" style="font-size: 24px; color: #2ecc71;"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 text-muted small">Notifications Lues</p>
                                    <h3 class="mb-0" id="readCount" style="color: #2ecc71; font-weight: 600;">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="row">
                <div class="col-lg-12">
                    <div id="notifications-list">
                        <div class="text-center py-5">
                            <div
                                style="width: 80px; height: 80px; background: rgba(28, 145, 30, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fa fa-hourglass-half" style="font-size: 40px; color: #1c911e;"></i>
                            </div>
                            <p class="text-muted">Chargement des notifications...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            background-color: #f5f7fa;
        }

        .notification-item {
            background: white;
            border: 1px solid #e0e0e0;
            border-left: 4px solid #1c911e;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .notification-item:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .notification-item.unread {
            background: linear-gradient(to right, rgba(28, 145, 30, 0.03) 0%, transparent 100%);
            border-left: 4px solid #1c911e;
            border-left-width: 6px;
        }

        .notification-badge-unread {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #1c911e;
            border-radius: 50%;
            margin-right: 8px;
        }

        .notification-title {
            font-weight: 600;
            color: #1c911e;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .notification-message {
            color: #555;
            margin-bottom: 12px;
            font-size: 14px;
            line-height: 1.5;
        }

        .notification-time {
            color: #999;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .notification-data {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 13px;
            border-left: 3px solid #1c911e;
        }

        .notification-data>div {
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .notification-data strong {
            color: #333;
        }

        .notification-data a {
            color: #1c911e;
            text-decoration: none;
            font-weight: 500;
        }

        .notification-data a:hover {
            text-decoration: underline;
        }

        .notification-actions {
            display: flex;
            gap: 8px;
        }

        .notification-actions .btn {
            border-radius: 6px;
            font-size: 13px;
            padding: 6px 12px;
            transition: all 0.2s ease;
            border: 1px solid #e0e0e0;
        }

        .notification-actions .btn:hover {
            transform: scale(1.05);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 60px;
            color: #1c911e;
            opacity: 0.3;
        }

        .empty-state p {
            color: #999;
            margin-top: 15px;
            font-size: 16px;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-control,
        .form-select {
            border: 1px solid #e0e0e0;
            background-color: white;
            transition: border-color 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #1c911e;
            box-shadow: 0 0 0 0.2rem rgba(28, 145, 30, 0.25);
        }
    </style>

    <script>
        let allNotifications = [];

        function loadAllNotifications() {
            fetch('/api/notifications/all?per_page=100', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    allNotifications = data.data || [];
                    updateStats();
                    filterAndDisplayNotifications();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('notifications-list').innerHTML = `
                    <div class="empty-state">
                        <i class="fa fa-exclamation-circle fa-3x mb-3"></i>
                        <p>Erreur lors du chargement des notifications</p>
                    </div>
                `;
                });
        }

        function updateStats() {
            const unreadCount = allNotifications.filter(n => !n.read_at).length;
            const readCount = allNotifications.filter(n => n.read_at).length;
            const totalCount = allNotifications.length;

            document.getElementById('unreadCount').textContent = unreadCount;
            document.getElementById('readCount').textContent = readCount;
            document.getElementById('totalCount').textContent = totalCount;
        }

        function filterAndDisplayNotifications() {
            const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const sortFilter = document.getElementById('sortFilter').value;

            let filtered = allNotifications.filter(notification => {
                const notifData = notification.data || {};
                const clientName = (notifData.client_nom || '').toLowerCase();
                const matchSearch = clientName.includes(searchTerm);
                const isUnread = !notification.read_at;

                if (statusFilter === 'unread' && !isUnread) return false;
                if (statusFilter === 'read' && isUnread) return false;

                return matchSearch;
            });

            if (sortFilter === 'oldest') {
                filtered.reverse();
            }

            displayAllNotifications(filtered);
        }

        function displayAllNotifications(notifications) {
            const container = document.getElementById('notifications-list');

            if (!notifications || notifications.length === 0) {
                container.innerHTML = `
                <div class="empty-state">
                    <i class="fa fa-inbox fa-3x mb-3"></i>
                    <p>Aucune notification</p>
                </div>
            `;
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                const notifData = notification.data || {};
                const isUnread = !notification.read_at;
                const date = new Date(notification.created_at);
                const timeStr = date.toLocaleString('fr-FR', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                html += `
                <div class="notification-item ${isUnread ? 'unread' : ''}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 15px;">
                        <div style="flex: 1; min-width: 0;">
                            <div class="notification-title">
                                ${isUnread ? '<span class="notification-badge-unread"></span>' : ''}
                                <i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>
                                ${notification.title}
                            </div>
                            <p class="notification-message">
                                ${notification.message}
                            </p>
                            <div class="notification-time">
                                <i class="fa fa-calendar"></i> ${timeStr}
                            </div>
                            ${notifData.numero_commande ? `
                                        <div class="notification-data">
                                            <div>
                                                <strong>Commande:</strong>
                                                <a href="/commandes/${notifData.commande_id}">#${notifData.numero_commande}</a>
                                            </div>
                                            <div>
                                                <strong>Client:</strong>
                                                <span>${notifData.client_nom || 'Inconnu'}</span>
                                            </div>
                                            <div>
                                                <strong>Téléphone:</strong>
                                                <span>${notifData.client_tel || '-'}</span>
                                            </div>
                                            <div>
                                                <strong>Montant:</strong>
                                                <span style="color: #1c911e; font-weight: 600;">${notifData.prix_total || '0'} Fcfa</span>
                                            </div>
                                            <div>
                                                <strong>Date Commande:</strong>
                                                <span>${notifData.date_commande ? new Date(notifData.date_commande).toLocaleString('fr-FR') : '-'}</span>
                                            </div>
                                        </div>
                                    ` : ''}
                        </div>
                        <div class="notification-actions">
                            ${isUnread ? `
                                        <button class="btn btn-sm btn-outline-success" onclick="markNotificationAsRead(${notification.id})" title="Marquer comme lu">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    ` : ''}
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification(${notification.id})" title="Supprimer">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            });

            container.innerHTML = html;
        }

        function deleteNotification(id) {
            if (confirm('Supprimer cette notification ?')) {
                fetch(`/api/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                    })
                    .then(() => loadAllNotifications())
                    .catch(error => console.error('Erreur:', error));
            }
        }

        function markNotificationAsRead(id) {
            fetch(`/api/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(() => loadAllNotifications())
                .catch(error => console.error('Erreur:', error));
        }

        function markAllAsRead() {
            if (confirm('Marquer toutes les notifications comme lues ?')) {
                fetch('/api/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(() => loadAllNotifications())
                    .catch(error => console.error('Erreur:', error));
            }
        }

        // Event Listeners for Filters
        document.getElementById('searchFilter').addEventListener('keyup', filterAndDisplayNotifications);
        document.getElementById('statusFilter').addEventListener('change', filterAndDisplayNotifications);
        document.getElementById('sortFilter').addEventListener('change', filterAndDisplayNotifications);

        document.addEventListener('DOMContentLoaded', loadAllNotifications);
    </script>
@endsection
