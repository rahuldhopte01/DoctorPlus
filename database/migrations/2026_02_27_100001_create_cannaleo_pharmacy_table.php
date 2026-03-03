<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cannaleo/Curobo API pharmacies – one row per distinct API pharmacy.
     */
    public function up(): void
    {
        Schema::create('cannaleo_pharmacy', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 191)->comment('pharmacy_id from API');
            $table->string('name');
            $table->string('domain')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique('external_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cannaleo_pharmacy');
    }
};
