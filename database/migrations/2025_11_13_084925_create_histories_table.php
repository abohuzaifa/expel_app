<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_read')->default(0);
            $table->boolean('is_start')->default(0);
            $table->boolean('is_end')->default(0);
            $table->timestamps();

            // Optional foreign keys
            // $table->foreign('request_id')->references('id')->on('requests')->onDelete('set null');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
