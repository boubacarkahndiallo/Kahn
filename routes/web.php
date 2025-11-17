<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\LivraisonController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\FirstLoginController;
use App\Http\Controllers\ProfileController;


// Route pour la vérification du client
Route::post('/check-client', [App\Http\Controllers\ClientAuthController::class, 'checkClient'])->name('check.client');

// Route pour la recherche de produits
Route::get('/search', [ProduitController::class, 'search'])->name('search.produits');

// Routes Home
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'home')->name('app_accueil');
    Route::get('/about', 'about')->name('app_about');
    Route::get('/contact', 'contact')->name('app_contact');
    Route::match(['get', 'post'], '/dashboard', 'dashboard')->middleware('auth')->name('app_dashboard');
});

// --- ROUTES AUTH / LOGIN ---
Route::controller(LoginController::class)->group(function () {
    Route::get('/logout', 'logout')->name('app_logout');
    Route::post('/exist_email', 'existEmail')->name('app_exist_email');
    Route::match(['get', 'post'], '/activation_code/{token}', 'activationCode')->name('app_activation_code');
    Route::get('/user_checker', 'userChecker')->name('app_user_checker');
    Route::get('/resend_activation_code/{token}', 'resendActivationCode')->name('app_resend_activation_code');
    Route::get('/activation_account_link/{token}', 'activationAccountLink')->name('app_activation_account_link');
    Route::match(['get', 'post'], '/activation_account_change_email/{token}', 'activationAccountChangeEmail')->name('app_activation_account_change_email');
});

// Routes pour la gestion du profil utilisateur (moi)
Route::middleware('auth')->group(function () {
    Route::put('/moi', [ProfileController::class, 'update'])->name('user.profile.update');

    // Route pour les notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');
});

// --- ROUTES ADMIN UTILISATEURS (Réservées au SUPER ADMIN uniquement) ---
Route::group(['prefix' => 'utilisateurs'], function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('/', 'index')->name('admin.users.index');      // Liste des utilisateurs
        Route::post('/', 'store')->name('admin.users.store');     // Créer via AJAX
        Route::get('/{id}/ajax-show', 'ajaxShow');               // Détails pour modal Voir
        Route::get('/{id}/ajax-edit', 'ajaxEdit');               // Données pour modal Éditer
        Route::put('/{id}', 'update');                           // Mettre à jour via AJAX
        Route::delete('/{id}', 'destroy');                       // Supprimer via AJAX
    });
});
//     });
// });

Route::controller(AdminController::class)->group(function () {
    Route::get('/utilisateurs', 'index')->name('admin.users.index');
    Route::get('/utilisateurs/create', 'create')->name('admin.users.create');
    Route::post('/utilisateurs', 'store')->name('admin.users.store');
    Route::get('/utilisateurs/{id}/edit', 'edit')->name('admin.users.edit');
    Route::put('/utilisateurs/{id}', 'update')->name('admin.users.update');
    Route::delete('/utilisateurs/{id}', 'destroy')->name('admin.users.destroy');

    // 🟢 Routes AJAX :
    Route::get('/utilisateurs/{id}/ajax-show', 'ajaxShow')->name('admin.users.ajaxShow');
    Route::get('/utilisateurs/{id}/ajax-edit', 'ajaxEdit')->name('admin.users.ajaxEdit');
    // Première connexion
    Route::get('/first-login/{token}', [FirstLoginController::class, 'showForm'])->name('first.login');
    Route::post('/first-login/{token}', [FirstLoginController::class, 'setPassword'])->name('first.login.set');
});

// Clients
Route::controller(ClientController::class)->group(function () {
    Route::get('clients', 'index')->name('clients.index'); // liste clients
    Route::get('clients/{id}', 'show')->name('clients.show'); // détail client
    Route::post('clients', 'store')->name('clients.store'); // créer client
    Route::put('clients/{id}', 'update')->name('clients.update'); // modifier client
    Route::delete('clients/{id}', 'destroy')->name('clients.destroy'); // supprimer client
    // Pour Ajax
    Route::get('clients/{id}/ajax-show', 'ajaxShow')->name('clients.ajaxShow');
    Route::get('clients/{id}/ajax-edit', 'ajaxEdit')->name('clients.ajaxEdit');
    Route::get('/clients/last', [ClientController::class, 'getLastClient']);
});

// Fournisseurs
Route::controller(FournisseurController::class)->group(function () {
    Route::get('fournisseurs', 'index')->name('fournisseurs.index');
    Route::get('fournisseurs/{id}', 'show')->name('ournisseurs.show');
    Route::post('fournisseurs', 'store')->name('fournisseurs.store');
    Route::put('fournisseurs/{id}', 'update')->name('fournisseurs.update');
    Route::delete('fournisseurs/{id}', 'destroy')->name('fournisseurs.destroy');
});


// QR Code
Route::get('/facture/{id}/qrcode', [QrCodeController::class, 'generate'])->name('facture.qrcode');

// Facture PDF
Route::controller(FactureController::class)->group(function () {
    Route::get('/facture/{id}', 'show')->name('facture.show');
    Route::get('/facture/{id}/download', 'download')->name('facture.download');
    Route::get('/facture/{id}/preview', 'preview')->name('facture.preview');
});

/*
|--------------------------------------------------------------------------
| Produits
|--------------------------------------------------------------------------
*/

// Produits
Route::controller(ProduitController::class)->group(function () {
    Route::get('produits', 'index')->name('produits.index');             // Liste de tous les produits
    Route::get('produits/ajout', 'create')->name('produits.create');      // Formulaire ajout produit
    Route::get('/showAll')->name('produits.showAll'); // Tous les produits
    Route::get('/allproduit', [ProduitController::class, 'allproduit'])->name('produits.allproduit');
    Route::get('produits/categorie/{categorie}', 'category')->name('produits.category');
    Route::get('/legumes', [ProduitController::class, 'legumes'])->name('app_legumes');


    // Routes dynamiques (doivent rester en bas)
    Route::get('produits/{id}', 'show')->name('produits.show');           // Voir un produit
    Route::post('produits', 'store')->name('produits.store');             // Créer un produit
    Route::put('produits/{id}', 'update')->name('produits.update');       // Modifier un produit
    Route::delete('produits/{id}', 'destroy')->name('produits.destroy');  // Supprimer un produit

    // AJAX
    Route::get('produits/{id}/ajax-show', 'ajaxShow')->name('produits.ajaxShow');
    Route::get('produits/{id}/ajax-edit', 'ajaxEdit')->name('produits.ajaxEdit');
});

/*
|--------------------------------------------------------------------------
| Commandes
|--------------------------------------------------------------------------
*/
Route::controller(CommandeController::class)->group(function () {
    Route::get('commandes', 'index')->name('commandes.index'); // liste commandes (admin)
    Route::get('commandes/{id}', 'show')->name('commandes.show'); // détail d’une commande
    Route::post('commandes', 'store')->name('commandes.store'); // créer une commande (client)
    Route::put('commandes/{id}', 'update')->name('commandes.update'); // mettre à jour (paiement, statut…)
    Route::delete('commandes/{id}', 'destroy')->name('commandes.destroy'); // annuler commande
});

/*
|--------------------------------------------------------------------------
| Livraison
|--------------------------------------------------------------------------
*/
Route::controller(LivraisonController::class)->group(function () {
    Route::get('livraisons', 'index')->name('livraisons.index'); // liste livraisons
    Route::get('livraisons/{id}', 'show')->name('livraisons.show'); // détail livraison
    Route::post('livraisons', 'store')->name('livraisons.store'); // créer livraison
    Route::put('livraisons/{id}', 'update')->name('livraisons.update'); // modifier livraison
    Route::delete('livraisons/{id}', 'destroy')->name('livraisons.destroy'); // supprimer livraison
});
