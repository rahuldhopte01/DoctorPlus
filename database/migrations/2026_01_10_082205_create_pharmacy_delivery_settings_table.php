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
        Schema::create('pharmacy_delivery_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('pharmacies')->onDelete('cascade');
            $table->enum('delivery_type', ['pickup_only', 'delivery_only', 'pickup_delivery'])->default('pickup_only');
            $table->decimal('delivery_radius', 8, 2)->nullable()->comment('Delivery radius in kilometers');
            $table->timestamps();
            
            $table->unique('pharmacy_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_delivery_settings');
    }
};
