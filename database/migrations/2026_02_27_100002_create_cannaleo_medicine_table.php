<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cannaleo/Curobo API products – one row per API product per pharmacy.
     */
    public function up(): void
    {
        Schema::create('cannaleo_medicine', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cannaleo_pharmacy_id');
            $table->string('external_id', 191)->comment('API id e.g. bedrocan-afina');
            $table->string('ansay_id')->nullable();
            $table->string('name');
            $table->string('category')->nullable();
            $table->boolean('is_api_medicine')->default(true);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('thc', 10, 2)->nullable();
            $table->decimal('cbd', 10, 2)->nullable();
            $table->string('genetic')->nullable();
            $table->string('strain')->nullable();
            $table->string('country')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('grower')->nullable();
            $table->string('availability')->nullable();
            $table->tinyInteger('irradiated')->nullable();
            $table->json('terpenes')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->foreign('cannaleo_pharmacy_id')->references('id')->on('cannaleo_pharmacy')->onDelete('cascade');
            $table->unique(['cannaleo_pharmacy_id', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cannaleo_medicine');
    }
};
