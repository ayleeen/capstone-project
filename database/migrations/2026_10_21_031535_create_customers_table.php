<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            // Optional link to a client account (null for walk-in customers)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->date('dob')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip', 20)->nullable();

            $table->string('customer_type', 30)->nullable();           // regular, vip, new
            $table->string('preferred_contact_method', 30)->nullable(); // email, phone, sms
            $table->string('status', 20)->default('active');            // active, inactive
            $table->json('notes')->nullable();                          // [{text, author, date}]

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};