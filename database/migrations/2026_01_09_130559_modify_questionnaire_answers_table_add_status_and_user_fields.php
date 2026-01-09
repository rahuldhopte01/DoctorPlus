<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, drop the existing foreign key constraint on appointment_id
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
        });
        
        // Make appointment_id nullable using raw SQL (Laravel's change() requires doctrine/dbal)
        DB::statement('ALTER TABLE questionnaire_answers MODIFY appointment_id BIGINT UNSIGNED NULL');
        
        // Add new fields for immediate storage
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('appointment_id');
            $table->unsignedBigInteger('category_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('questionnaire_id')->nullable()->after('category_id');
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending')->after('questionnaire_id');
            $table->timestamp('submitted_at')->nullable()->after('status');
        });
        
        // Re-add foreign key for appointment_id (now nullable)
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointment')
                ->onDelete('cascade');
        });
        
        // Add foreign key constraints for new fields
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
                
            $table->foreign('category_id')
                ->references('id')
                ->on('category')
                ->onDelete('cascade');
                
            $table->foreign('questionnaire_id')
                ->references('id')
                ->on('questionnaires')
                ->onDelete('cascade');
        });
        
        // Add indexes
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->index(['user_id', 'category_id', 'questionnaire_id', 'status'], 'qa_user_cat_quest_status_idx');
            $table->index(['user_id', 'status'], 'qa_user_status_idx');
            $table->index(['category_id', 'status'], 'qa_category_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['questionnaire_id']);
            $table->dropForeign(['appointment_id']);
        });
        
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('qa_user_cat_quest_status_idx');
            $table->dropIndex('qa_user_status_idx');
            $table->dropIndex('qa_category_status_idx');
            
            // Drop columns
            $table->dropColumn(['user_id', 'category_id', 'questionnaire_id', 'status', 'submitted_at']);
        });
        
        // Make appointment_id required again using raw SQL
        DB::statement('ALTER TABLE questionnaire_answers MODIFY appointment_id BIGINT UNSIGNED NOT NULL');
        
        // Re-add foreign key for appointment_id
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointment')
                ->onDelete('cascade');
        });
    }
};
