<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds reviewing_doctor_id and hospital_id to questionnaire_answers
     * Updates status enum to include IN_REVIEW (keeping under_review for backward compatibility)
     */
    public function up(): void
    {
        // First, update status enum to include IN_REVIEW
        // We'll keep 'under_review' for backward compatibility but use 'IN_REVIEW' going forward
        DB::statement("
            ALTER TABLE questionnaire_answers 
            MODIFY COLUMN status ENUM('pending', 'under_review', 'IN_REVIEW', 'approved', 'rejected', 'REVIEW_COMPLETED') 
            DEFAULT 'pending'
        ");
        
        // Add reviewing_doctor_id field
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('reviewing_doctor_id')->nullable()->after('status');
        });
        
        // Add hospital_id field
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->after('reviewing_doctor_id');
        });
        
        // Add foreign key constraints
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->foreign('reviewing_doctor_id')
                ->references('id')
                ->on('doctor')
                ->onDelete('set null')
                ->onUpdate('cascade');
                
            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospital')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        
        // Add indexes for efficient querying
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->index(['hospital_id', 'status'], 'qa_hospital_status_idx');
            $table->index(['reviewing_doctor_id', 'status'], 'qa_reviewing_doctor_status_idx');
            $table->index(['hospital_id', 'reviewing_doctor_id'], 'qa_hospital_doctor_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->dropIndex('qa_hospital_status_idx');
            $table->dropIndex('qa_reviewing_doctor_status_idx');
            $table->dropIndex('qa_hospital_doctor_idx');
        });
        
        // Drop foreign key constraints
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->dropForeign(['reviewing_doctor_id']);
            $table->dropForeign(['hospital_id']);
        });
        
        // Drop columns
        Schema::table('questionnaire_answers', function (Blueprint $table) {
            $table->dropColumn(['reviewing_doctor_id', 'hospital_id']);
        });
        
        // Revert status enum (remove IN_REVIEW and REVIEW_COMPLETED)
        DB::statement("
            ALTER TABLE questionnaire_answers 
            MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected') 
            DEFAULT 'pending'
        ");
    }
};
