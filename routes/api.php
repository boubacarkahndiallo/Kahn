// Dans routes/api.php

Route::post('/check-client', [App\Http\Controllers\ClientAuthController::class, 'checkClient']);

Route::middleware(['auth'])->group(function() {
Route::get('/mes-commandes', [CommandeController::class, 'mesCommandes']);

// Routes de notifications pour les admins
Route::prefix('notifications')->group(function() {
Route::get('/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread']);
Route::get('/all', [\App\Http\Controllers\NotificationController::class, 'getAll']);
Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'delete']);
});
});