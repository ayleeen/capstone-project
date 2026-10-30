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
    Schema::table('reservations', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->after('id');
        $table->unsignedBigInteger('package_id')->nullable()->after('package');
        $table->string('landmark')->nullable();
        $table->text('food_restriction')->nullable();
        $table->decimal('down_payment', 10, 2)->default(0);
        $table->string('proof_of_payment')->nullable();
    });
}

public function down(): void
{
    Schema::table('reservations', function (Blueprint $table) {
        $table->dropColumn(['user_id', 'package_id', 'landmark', 'food_restriction', 'down_payment', 'proof_of_payment']);
    });
}
};
