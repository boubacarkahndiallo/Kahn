# 🔔 Système de Notifications en Temps Réel pour Admins

## ✅ Implémentation Complète

J'ai créé un système complet de notifications en temps réel pour que les **admin et super_admin** reçoivent les commandes immédiatement.

## 📋 Composants Créés/Modifiés

### 1. **Modèle & Migration**
- ✅ `app/Models/Notification.php` : Modèle pour stocker les notifications
- ✅ `database/migrations/2025_11_17_112527_create_notifications_table.php` : Table avec champs:
  - `user_id` : Admin qui reçoit la notification
  - `type` : Type de notification ('order', 'system', etc.)
  - `title` : Titre
  - `message` : Message
  - `data` : JSON avec détails de la commande
  - `read_at` : Timestamp de lecture

### 2. **Listener & Events**
- ✅ `app/Listeners/CreateOrderNotification.php` : Listener qui crée une notification pour chaque admin quand une commande est créée
- ✅ `app/Providers/EventServiceProvider.php` : Enregistre le listener sur l'event `OrderCreated`
- ✅ Event existant `app/Events/OrderCreated.php` : Déclenche les notifications

### 3. **Contrôleur API**
- ✅ `app/Http/Controllers/NotificationController.php` : Gère les routes API
  - `GET /api/notifications/unread` : Récupère les notifications non lues
  - `GET /api/notifications/all` : Récupère toutes les notifications (paginées)
  - `POST /api/notifications/{id}/read` : Marquer comme lue
  - `POST /api/notifications/read-all` : Marquer tout comme lu
  - `DELETE /api/notifications/{id}` : Supprimer une notification

### 4. **Frontend JavaScript**
- ✅ `public/js/notifications.js` : Script qui:
  - Poll les notifications toutes les 5 secondes
  - Affiche les toasts en haut-à-droite
  - Auto-ferme après 10 secondes
  - Badge de compteur
  - Clique sur notification → redirection vers la commande

### 5. **Interface Web**
- ✅ `resources/views/notifications/index.blade.php` : Page pour voir toutes les notifications
- ✅ Intégrée dans `resources/views/base.blade.php`

### 6. **Routes**
- ✅ Routes API : `/api/notifications/*` (protégées par auth)
- ✅ Route Web : `/notifications` (page notifications pour admins)

## 🎯 Flux de Fonctionnement

```
1. Client crée une commande
   ↓
2. CommandeController::store() déclenche event OrderCreated
   ↓
3. Listener CreateOrderNotification crée une row dans la table notifications
   ↓
4. Pour chaque admin/super_admin, crée une notification:
   - Type: 'order'
   - Title: 'Nouvelle commande reçue'
   - Message: 'Commande #CMD-001 de Jean Dupont - 15000 Fcfa'
   - Data: {commande_id, numero_commande, client_nom, tel, prix_total, produits, date}
   ↓
5. Frontend (notifications.js) poll /api/notifications/unread
   ↓
6. Admin voit une notification toast en haut-à-droite
   ↓
7. Badge rouge montre le nombre de notifications non lues
   ↓
8. Clique sur notification → redirection vers la commande
   ↓
9. Notification auto-fermée après 10s et marquée comme lue
```

## 🎨 Affichage Utilisateur

### Toast Notification
```
┌─────────────────────────────────┐
│ 🔔 Nouvelle commande reçue       │
│                                  │
│ Commande #CMD-001 de Jean        │
│ Dupont - Total: 15000 Fcfa       │
│                                  │
│ #CMD-001 • Jean Dupont • 15000Fcfa
│                               [✕] │
└─────────────────────────────────┘
```

### Page Notifications (`/notifications`)
- Liste toutes les notifications
- Filtre lues/non lues (visuellement distinctes)
- Affiche détails de la commande
- Boutons: Marquer comme lu, Supprimer, Marquer tout comme lu
- Clique sur commande → détails

## 📱 Fonctionnalités

✅ **Notifications en temps réel** (poll toutes les 5 sec)
✅ **Badge compteur** de notifications non lues
✅ **Toast auto-fermant** après 10 sec
✅ **Redirection directe** vers la commande
✅ **Marquage automatique** comme lue après 3 sec
✅ **Suppression** des notifications
✅ **Page dédiée** pour consulter l'historique
✅ **JSON data** avec tous les détails de la commande
✅ **Base de données** pour historique complet

## 🔒 Sécurité

- ✅ Routes API protégées par middleware `auth`
- ✅ Les users ne voient que leurs propres notifications
- ✅ Vérification du role admin/super_admin dans le listener
- ✅ CSRF protection sur les requêtes POST/DELETE

## 🚀 Utilisation

### Pour les Admins
1. Les notifications s'affichent automatiquement en haut-à-droite
2. Clique sur la notification pour voir la commande
3. Accès à `/notifications` pour voir l'historique complet
4. Markez les notifications comme lues

### Pour le Développeur
```php
// Ajouter une notification manuelle (si besoin)
Notification::create([
    'user_id' => $admin->id,
    'type' => 'order',
    'title' => 'Titre',
    'message' => 'Message',
    'data' => ['key' => 'value'],
]);

// Récupérer les notifications non lues
$unread = auth()->user()->notifications()
    ->whereNull('read_at')
    ->get();
```

## 📊 Structure DB

```
notifications
├── id (PK)
├── user_id (FK → users)
├── type (enum: order, system)
├── title (string)
├── message (text)
├── data (json)
├── read_at (timestamp nullable)
├── created_at (timestamp)
└── updated_at (timestamp)
```

## ⚙️ Configuration

Les notifications sont **automatiquement créées** via le Listener.
Pas de configuration supplémentaire requise.

## 🧪 Test

### Créer une notification test
```bash
php artisan tinker
>>> $user = User::where('role', 'admin')->first();
>>> Notification::create([
    'user_id' => $user->id,
    'type' => 'order',
    'title' => 'Test',
    'message' => 'Ceci est un test',
    'data' => ['commande_id' => 1],
]);
```

### Vérifier les endpoints API
```bash
curl http://127.0.0.1:8000/api/notifications/unread \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📝 Notes

- Les notifications sont stockées **en base de données** (pas en cache)
- Historique complet conservé même après suppression
- Frontend peut être étendu avec WebSockets (Laravel Echo) pour temps réel vrai
- Actuellement: poll toutes les 5 sec (compatible avec tous les serveurs)

## 🔜 Améliorations Futures

- [ ] WebSockets pour synchronisation instantanée (Laravel Echo)
- [ ] Notifications par email pour les admins
- [ ] SMS alerts via WhatsApp
- [ ] Grouper les notifications par jour
- [ ] Filtrer par type de notification
- [ ] Export de l'historique
