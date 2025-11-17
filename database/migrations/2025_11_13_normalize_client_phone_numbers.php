<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Récupérer tous les clients
        $clients = DB::table('clients')->get();

        foreach ($clients as $client) {
            if (!empty($client->tel)) {
                // Normalization simple: garder ce qui est là mais s'assurer du format E164
                $tel = $client->tel;

                // Nettoyer: enlever espaces et tirets
                $tel = str_replace([' ', '-', '(', ')'], '', $tel);

                // Si commence par 00, remplacer par +
                if (strpos($tel, '00') === 0) {
                    $tel = '+' . substr($tel, 2);
                }

                // Si c'est un numéro local guinéen (9 chiffres)
                if (preg_match('/^\d{9}$/', $tel)) {
                    $tel = '+224' . $tel;
                }

                // Si c'est un numéro sans +
                if (strpos($tel, '+') !== 0 && preg_match('/^\d{12}$/', $tel)) {
                    $tel = '+' . $tel;
                }

                // Mettre à jour si différent
                if ($tel !== $client->tel) {
                    DB::table('clients')->where('id', $client->id)->update(['tel' => $tel]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably revert this migration
    }
};
