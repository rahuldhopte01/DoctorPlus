<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->json('sub_answers')->nullable()->after('flag_reason');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->dropColumn('sub_answers');
        });
    }
};
