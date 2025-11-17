<!-- Notification Bell Icon for Admin Navbar -->
@auth
    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
        <li class="nav-item">
            <a href="{{ route('notifications.index') }}" class="nav-link" style="position: relative;">
                <i class="fa fa-bell" style="font-size: 18px; color: #1c911e;"></i>
                <span id="notification-badge-navbar" class="badge badge-danger"
                    style="
                    position: absolute;
                    top: -5px;
                    right: -5px;
                    background-color: #dc3545;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    display: none;
                ">0</span>
            </a>
        </li>
    @endif
@endauth

<script>
    // Mettre à jour le badge toutes les 10 secondes
    function updateNotificationBadgeNavbar() {
        fetch('/api/notifications/unread', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge-navbar');
                if (badge) {
                    const count = data.unread_count || 0;
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.style.display = count > 0 ? 'flex' : 'none';
                }
            })
            .catch(error => console.warn('Erreur badge notification:', error));
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateNotificationBadgeNavbar();
        setInterval(updateNotificationBadgeNavbar, 10000);
    });
</script>
