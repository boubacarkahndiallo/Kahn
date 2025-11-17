<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->id();

            // Informations générales sur le bien
            $table->string('nom');                      // Nom du bien
            $table->string('type');                     // Type de bien : appartement, maison, terrain, bureau...
            $table->text('description')->nullable();    // Description détaillée

            // Localisation
            $table->string('adresse')->nullable();      // Adresse complète
            $table->string('ville')->nullable();        // Ville
            $table->string('quartier')->nullable();     // Quartier
            $table->string('pays')->default('Guinée');  // Pays par défaut

            // Caractéristiques physiques
            $table->integer('superficie')->nullable();  // Superficie en m²
            $table->integer('chambres')->nullable();    // Nombre de chambres
            $table->integer('salons')->nullable();      // Nombre de salons
            $table->integer('douches')->nullable();     // Nombre de douches
            $table->integer('etages')->nullable();      // Nombre d'étages

            // Prix et statut
            $table->decimal('prix', 12, 2)->nullable(); // Prix du bien
            $table->enum('statut', ['disponible', 'loué', 'vendu'])
                ->default('disponible');

            // Images
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();

            // Propriétaire ou utilisateur lié
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
