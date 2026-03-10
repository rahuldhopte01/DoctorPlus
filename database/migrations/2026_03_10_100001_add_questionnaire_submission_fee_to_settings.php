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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'questionnaire_submission_fee')) {
                $table->decimal('questionnaire_submission_fee', 10, 2)->nullable()->after('prescription_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'questionnaire_submission_fee')) {
                $table->dropColumn('questionnaire_submission_fee');
            }
        });
    }
};
