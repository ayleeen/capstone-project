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
    Schema::table('reservations', function (Blueprint $t) {
        if (!Schema::hasColumn('reservations', 'custom_menu'))    $t->json('custom_menu')->nullable();
        if (!Schema::hasColumn('reservations', 'addons'))         $t->json('addons')->nullable();
        if (!Schema::hasColumn('reservations', 'price_per_head')) $t->unsignedInteger('price_per_head')->nullable();
    });
}

public function down(): void
{
    Schema::table('reservations', function (Blueprint $t) {
        $t->dropColumn(['custom_menu', 'addons', 'price_per_head']);
    });
}
};
