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
        Schema::table('appointment', function (Blueprint $table) {
            $table->foreignId('questionnaire_id')->nullable()->after('hospital_id')->constrained('questionnaires')->onDelete('set null');
            $table->timestamp('questionnaire_completed_at')->nullable()->after('questionnaire_id');
            $table->boolean('questionnaire_blocked')->default(false)->after('questionnaire_completed_at');
            $table->boolean('questionnaire_locked')->default(false)->after('questionnaire_blocked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment', function (Blueprint $table) {
            $table->dropForeign(['questionnaire_id']);
            $table->dropColumn(['questionnaire_id', 'questionnaire_completed_at', 'questionnaire_blocked', 'questionnaire_locked']);
        });
    }
};

