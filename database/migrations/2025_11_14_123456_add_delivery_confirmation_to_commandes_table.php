<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->timestamp('confirmation_requested_at')->nullable()->after('date_commande');
            $table->boolean('delivery_confirmed')->nullable()->default(null)->after('confirmation_requested_at');
            $table->timestamp('delivery_confirmed_at')->nullable()->after('delivery_confirmed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['confirmation_requested_at', 'delivery_confirmed', 'delivery_confirmed_at']);
        });
    }
};
