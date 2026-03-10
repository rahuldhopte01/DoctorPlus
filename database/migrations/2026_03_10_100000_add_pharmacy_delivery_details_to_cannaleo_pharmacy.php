<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add contact, address, and delivery details from Curobo pharmacies API.
     */
    public function up(): void
    {
        Schema::table('cannaleo_pharmacy', function (Blueprint $table) {
            // Contact
            $table->string('email')->nullable()->after('domain');
            $table->string('phone_number')->nullable()->after('email');

            // Address
            $table->string('street')->nullable()->after('phone_number');
            $table->string('plz', 20)->nullable()->after('street');
            $table->string('city')->nullable()->after('plz');

            // Shipping
            $table->string('shipping')->nullable()->after('city');
            $table->decimal('shipping_cost_standard', 10, 2)->nullable()->after('shipping');
            $table->json('shipping_cost_reduced')->nullable()->after('shipping_cost_standard');

            // Express
            $table->string('express')->nullable()->after('shipping_cost_reduced');
            $table->decimal('express_cost_standard', 10, 2)->nullable()->after('express');
            $table->json('express_cost_reduced')->nullable()->after('express_cost_standard');

            // Local courier (API uses typo "local_coure_*")
            $table->string('local_courier')->nullable()->after('express_cost_reduced');
            $table->decimal('local_courier_cost_standard', 10, 2)->nullable()->after('local_courier');
            $table->json('local_courier_cost_reduced')->nullable()->after('local_courier_cost_standard');

            // Pickup
            $table->string('pickup')->nullable()->after('local_courier_cost_reduced');
            $table->json('pickup_branches')->nullable()->after('pickup');
        });
    }

    public function down(): void
    {
        Schema::table('cannaleo_pharmacy', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'phone_number',
                'street',
                'plz',
                'city',
                'shipping',
                'shipping_cost_standard',
                'shipping_cost_reduced',
                'express',
                'express_cost_standard',
                'express_cost_reduced',
                'local_courier',
                'local_courier_cost_standard',
                'local_courier_cost_reduced',
                'pickup',
                'pickup_branches',
            ]);
        });
    }
};
