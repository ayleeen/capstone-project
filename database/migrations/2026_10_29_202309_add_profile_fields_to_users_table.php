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
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'secondary_phone')) {
            $table->string('secondary_phone', 30)->nullable();
        }
        if (!Schema::hasColumn('users', 'address_line1')) {
            $table->string('address_line1')->nullable();
        }
        if (!Schema::hasColumn('users', 'address_line2')) {
            $table->string('address_line2')->nullable();
        }
        if (!Schema::hasColumn('users', 'profile_photo')) {
            $table->string('profile_photo')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['secondary_phone', 'address_line1', 'address_line2', 'profile_photo']);
    });
}
};
