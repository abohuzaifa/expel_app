<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('item_quantity')->default(1);
            $table->decimal('item_tax', 10, 2)->default(0);
            $table->decimal('item_discount', 10, 2)->default(0);
            $table->decimal('item_total', 10, 2)->default(0);
            $table->text('item_description')->nullable();
            $table->timestamps();

            // Optional foreign keys
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
