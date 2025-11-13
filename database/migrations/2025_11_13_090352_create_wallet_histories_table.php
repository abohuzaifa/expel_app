<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id')->index();
            $table->decimal('amount', 10, 2);
            $table->boolean('is_deposite')->default(false);
            $table->boolean('is_expanse')->default(false);
            $table->string('description')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('invoice_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // foreign keys
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_histories');
    }
};
