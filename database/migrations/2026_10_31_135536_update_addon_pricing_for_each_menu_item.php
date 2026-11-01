<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->boolean('is_addon')->default(false)->after('is_active');
        });

        Schema::table('addon_prices', function (Blueprint $table) {
            $table->dropUnique(['servings']);

            $table->foreignId('menu_item_id')
                ->nullable()
                ->after('id')
                ->constrained('menu_items')
                ->cascadeOnDelete();

            $table->unique(['menu_item_id', 'servings']);
        });
    }

    public function down(): void
    {
        Schema::table('addon_prices', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
            $table->dropUnique(['menu_item_id', 'servings']);
            $table->dropColumn('menu_item_id');
            $table->unique('servings');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('is_addon');
        });
    }
};
