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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('image')->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->enum('user_type', ['admin', 'customer', 'employee'])->default('customer');
            $table->string('password')->nullable();
            $table->boolean('status')->default(1);
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('otp')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('driving_license')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank_account')->nullable();
            $table->text('device_token')->nullable();
            $table->boolean('is_available')->default(1);

            $table->timestamps();

            // Optional: Add foreign keys
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            // $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
