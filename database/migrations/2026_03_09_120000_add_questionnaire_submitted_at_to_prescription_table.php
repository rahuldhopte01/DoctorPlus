<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Links a questionnaire-sourced prescription to the specific answer batch (submitted_at)
     * so we only show "prescription generated" for the review that actually generated it.
     */
    public function up(): void
    {
        Schema::table('prescription', function (Blueprint $table) {
            $table->dateTime('questionnaire_submitted_at')->nullable()->after('appointment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription', function (Blueprint $table) {
            $table->dropColumn('questionnaire_submitted_at');
        });
    }
};
