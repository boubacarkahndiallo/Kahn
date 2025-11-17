<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Champs personnels
            if (!Schema::hasColumn('users', 'nom')) {
                $table->string('nom')->nullable();
            }
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom')->nullable();
            }
            if (!Schema::hasColumn('users', 'tel')) {
                $table->string('tel')->unique()->nullable();
            }
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->unique()->nullable();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['user', 'admin', 'super_admin'])->default('user');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nom', 'prenom', 'tel', 'email', 'role']); 
        });
    }
};
