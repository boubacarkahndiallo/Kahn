<?php

use App\Http\Controllers\AccueilController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ProduitController;
use Illuminate\Support\Facades\Route;

Route::get('',[AccueilController::class,'index'])->name('accueil');
Route::get('produits',[ProduitController::class,'index'])->name('produits.index');
Route::get('clients',[ClientController::class,'index'])->name('clients.index');
Route::post('clients',[ClientController::class,'store'])->name('clients.store');
Route::delete('clients/{id}',[ClientController::class,'destroy'])->name('clients.destroy');
Route::get('categories',[CategorieController::class,'index'])->name('categories.index');
Route::get('fournisseurs',[FournisseurController::class,'index'])->name('fournisseurs.index');

Route::get('inscription',[AuthController::class,'inscription'])->name('inscription');
Route::get('connexion',[AuthController::class,'connexion'])->name('connexion');



