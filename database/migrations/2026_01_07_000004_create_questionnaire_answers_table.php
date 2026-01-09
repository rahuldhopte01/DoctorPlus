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
        Schema::create('questionnaire_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointment')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questionnaire_questions')->onDelete('cascade');
            $table->integer('questionnaire_version')->default(1);
            $table->text('answer_value')->nullable(); // Text/JSON for checkbox arrays
            $table->string('file_path')->nullable(); // For file uploads
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            $table->timestamps();

            $table->index(['appointment_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};



