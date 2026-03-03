<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Optional: log each catalog sync run for visibility.
     */
    public function up(): void
    {
        Schema::create('cannaleo_sync_log', function (Blueprint $table) {
            $table->id();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 20)->default('started'); // started, completed, failed
            $table->unsignedInteger('items_fetched')->default(0);
            $table->unsignedInteger('pharmacies_created')->default(0);
            $table->unsignedInteger('pharmacies_updated')->default(0);
            $table->unsignedInteger('medicines_created')->default(0);
            $table->unsignedInteger('medicines_updated')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cannaleo_sync_log');
    }
};
