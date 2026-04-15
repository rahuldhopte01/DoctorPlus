<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store the pickup branch ID selected by the patient when cannaleo_delivery_option = 'pickup'.
     * Sent as pickup_branch_id to the Curobo prescription API.
     */
    public function up(): void
    {
        Schema::table('questionnaire_submissions', function (Blueprint $table) {
            $table->string('cannaleo_pickup_branch_id', 128)->nullable()->after('cannaleo_delivery_option');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_submissions', function (Blueprint $table) {
            $table->dropColumn('cannaleo_pickup_branch_id');
        });
    }
};
