<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds Cannaleo partner flow: selected_cannaleo_pharmacy_id and delivery_type 'cannaleo'.
     */
    public function up(): void
    {
        Schema::table('questionnaire_submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('selected_cannaleo_pharmacy_id')->nullable()->after('selected_pharmacy_id');
        });

        // Add FK after cannaleo_pharmacy table exists (it should from Cannaleo migrations)
        if (Schema::hasTable('cannaleo_pharmacy')) {
            Schema::table('questionnaire_submissions', function (Blueprint $table) {
                $table->foreign('selected_cannaleo_pharmacy_id')
                    ->references('id')
                    ->on('cannaleo_pharmacy')
                    ->onDelete('set null');
            });
        }

        // Extend delivery_type enum to include 'cannaleo' (MySQL)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            \DB::statement("ALTER TABLE questionnaire_submissions MODIFY COLUMN delivery_type ENUM('delivery', 'pickup', 'cannaleo') NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cannaleo_pharmacy')) {
            Schema::table('questionnaire_submissions', function (Blueprint $table) {
                $table->dropForeign(['selected_cannaleo_pharmacy_id']);
            });
        }
        Schema::table('questionnaire_submissions', function (Blueprint $table) {
            $table->dropColumn('selected_cannaleo_pharmacy_id');
        });
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            \DB::statement("ALTER TABLE questionnaire_submissions MODIFY COLUMN delivery_type ENUM('delivery', 'pickup') NULL");
        }
    }
};
