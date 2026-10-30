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
    Schema::create('phone_otps', function (Blueprint $table) {
        $table->id();
        $table->string('phone')->index();
        $table->string('code');
        $table->string('channel')->default('sms'); // sms or whatsapp
        $table->timestamp('expires_at');
        $table->unsignedTinyInteger('attempts')->default(0);
        $table->timestamps();
    });
}
};
