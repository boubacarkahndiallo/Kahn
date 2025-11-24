# Notifications en temps réel - Configuration rapide

Cette note explique comment activer et tester le système de notifications temps réel implémenté.

1) Choisir et configurer un driver de broadcast

- Exemple Pusher (service externe)

Dans votre `.env` :

```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=mt1
```

Assurez-vous aussi d'exposer les variables pour le build front (Vite) :

```
VITE_PUSHER_APP_KEY=${PUSHER_APP_KEY}
VITE_PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER}
```

- Exemple Laravel Websockets (local)

```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=localkey
PUSHER_APP_SECRET=localsecret
PUSHER_APP_CLUSTER=mt1
WEBSOCKETS_PORT=6001
```

Installez `beyondcode/laravel-websockets` si nécessaire et suivez sa doc.

2) Installer les dépendances front

```powershell
npm install --save laravel-echo pusher-js
npm run dev
```

3) Lancer le worker de queue (les listeners sont queueables)

```powershell
php artisan queue:work
```

4) Endpoints utiles (déjà ajoutés)

- `GET /notifications/list` -> liste des notifications (JSON)
- `GET /notifications/unread-count` -> nombre de non-lues
- `POST /notifications/{id}/read` -> marque une notification comme lue
- `POST /notifications/read-all` -> marque toutes les notifications comme lues

5) Intégration client

- Le layout `resources/views/base.blade.php` inclut `public/js/notifications.js`.
- Si vous compilez avec Vite, vous pouvez utiliser `resources/js/echo.js` et `resources/js/notifications-client.js` puis build.

6) Tests

- Créer un produit via l'admin -> doit créer des notifications en base et broadcast.
- Passer une commande -> doit créer des notifications pour admin + client et broadcast.

7) Remarques

- Channels privés (`user.{id}.notifications`) nécessitent que le broadcasting auth fonctionne (`/broadcasting/auth`).
- Adaptez l'affichage des toasts à votre UI (Bootstrap toast, SweetAlert, etc.).

