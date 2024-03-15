<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //category table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('p_name');
            $table->string('p_name_ar')->nullable();
            $table->string('images')->nullable();
            $table->integer('shop_id')->default(0);
            $table->integer('status')->default(1);
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('products');
    }
};
