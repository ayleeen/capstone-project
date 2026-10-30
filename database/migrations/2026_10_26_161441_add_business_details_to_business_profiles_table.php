
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('business_name')
                ->default("Lolay's Catering Services");

            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('logo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'description',
                'email',
                'contact_number',
                'address',
                'website',
                'facebook',
                'logo',
            ]);
        });
    }
};
