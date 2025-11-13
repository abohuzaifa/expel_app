<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('paid', 10, 2)->default(0);
            $table->decimal('due', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('order_status')->nullable();
            $table->boolean('is_approve')->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('payment_method')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_address')->nullable();
            $table->string('user_mobile')->nullable();
            $table->boolean('manual_order')->default(0);
            $table->timestamp('pickup_date_time')->nullable();
            $table->date('inv_date')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->text('seller_description')->nullable();
            $table->timestamps();

            // Optional foreign keys
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('seller_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('payment_method')->references('id')->on('payment_methods')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
