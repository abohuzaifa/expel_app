<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->text('images')->nullable();
            $table->decimal('parcel_lat', 10, 7)->nullable();
            $table->decimal('parcel_long', 10, 7)->nullable();
            $table->string('parcel_address')->nullable();
            $table->decimal('receiver_lat', 10, 7)->nullable();
            $table->decimal('receiver_long', 10, 7)->nullable();
            $table->string('receiver_address')->nullable();
            $table->string('receiver_mobile')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->string('channel_name')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('payment_method')->nullable();
            $table->timestamps();

            // Optional foreign keys
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('offer_id')->references('id')->on('offers')->onDelete('set null');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            // $table->foreign('payment_method')->references('id')->on('payment_methods')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
