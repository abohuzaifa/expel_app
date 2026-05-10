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
        if (!Schema::hasTable('user_bank_accounts')) {
            Schema::create('user_bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('bank_id');
                $table->string('account_holder_name');
                $table->string('account_number');
                $table->string('branch_code')->nullable();
                $table->string('iban')->nullable();
                $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('verification_notes')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('bank_id')->references('id')->on('banks')->onDelete('restrict');
                $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');

                $table->index('user_id');
                $table->index('verification_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_accounts');
    }
};
