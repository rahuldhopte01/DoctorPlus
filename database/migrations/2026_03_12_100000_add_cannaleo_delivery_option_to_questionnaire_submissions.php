<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Store the customer's selected Cannaleo delivery option (shipping, express, local_courier, pickup)
     * for the prescription API.
     */
    public function up(): void
    {
        Schema::table('questionnaire_submissions', function (Blueprint $table) {
            $table->string('cannaleo_delivery_option', 32)->nullable()->after('selected_cannaleo_pharmacy_id');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_submissions', function (Blueprint $table) {
            $table->dropColumn('cannaleo_delivery_option');
        });
    }
};
