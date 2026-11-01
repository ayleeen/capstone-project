<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addon_prices', function (Blueprint $t) {
            $t->id();
            $t->unsignedSmallInteger('servings')->unique();
            $t->decimal('price', 10, 2);
            $t->timestamps();
        });

        DB::table('addon_prices')->insert([
            ['servings' => 30, 'price' => 1500, 'created_at' => now(), 'updated_at' => now()],
            ['servings' => 40, 'price' => 2500, 'created_at' => now(), 'updated_at' => now()],
            ['servings' => 50, 'price' => 3500, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('reservations', function (Blueprint $t) {
            $t->json('addons')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', fn (Blueprint $t) => $t->dropColumn('addons'));
        Schema::dropIfExists('addon_prices');
    }
};

