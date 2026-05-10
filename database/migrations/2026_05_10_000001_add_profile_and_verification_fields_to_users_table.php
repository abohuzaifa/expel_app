<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'street_address')) {
                $table->string('street_address')->nullable();
            }

            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }

            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable();
            }

            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }

            if (!Schema::hasColumn('users', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }

            if (!Schema::hasColumn('users', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }

            if (!Schema::hasColumn('users', 'number_plate')) {
                $table->string('number_plate')->nullable();
            }

            if (!Schema::hasColumn('users', 'iban')) {
                $table->string('iban')->nullable();
            }

            if (!Schema::hasColumn('users', 'twitter')) {
                $table->string('twitter')->nullable();
            }

            if (!Schema::hasColumn('users', 'facebook')) {
                $table->string('facebook')->nullable();
            }

            if (!Schema::hasColumn('users', 'instagram')) {
                $table->string('instagram')->nullable();
            }

            if (!Schema::hasColumn('users', 'linkedin')) {
                $table->string('linkedin')->nullable();
            }

            if (!Schema::hasColumn('users', 'is_read')) {
                $table->boolean('is_read')->default(0);
            }

            if (!Schema::hasColumn('users', 'driving_license_image')) {
                $table->string('driving_license_image')->nullable();
            }

            if (!Schema::hasColumn('users', 'vehicle_registration_image')) {
                $table->string('vehicle_registration_image')->nullable();
            }

            if (!Schema::hasColumn('users', 'verification_status')) {
                $table->string('verification_status')->default('pending');
            }

            if (!Schema::hasColumn('users', 'verification_notes')) {
                $table->text('verification_notes')->nullable();
            }

            if (!Schema::hasColumn('users', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
            }

            if (!Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'street_address',
                'city',
                'state',
                'postal_code',
                'latitude',
                'longitude',
                'number_plate',
                'iban',
                'twitter',
                'facebook',
                'instagram',
                'linkedin',
                'is_read',
                'driving_license_image',
                'vehicle_registration_image',
                'verification_status',
                'verification_notes',
                'verified_by',
                'verified_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};