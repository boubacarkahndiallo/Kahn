<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            
            // 🔢 Numéro de commande unique
            $table->string('numero_commande')->unique();

            // 🔗 Client lié
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            // 🧾 Produits au format JSON
            $table->json('produits');

            // 💰 Prix total
            $table->decimal('prix_total', 15, 2);

            // 🚦 Statut (en_cours, livree, annulee)
            $table->enum('statut', ['en_cours', 'livree', 'annulee'])->default('en_cours');

            // 📅 Date commande
            $table->timestamp('date_commande')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('commandes');
    }
};
