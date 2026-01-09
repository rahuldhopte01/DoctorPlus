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
        Schema::create('questionnaire_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('questionnaire_sections')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('field_type', ['text', 'textarea', 'number', 'dropdown', 'radio', 'checkbox', 'file'])->default('text');
            $table->json('options')->nullable(); // For dropdown/radio/checkbox options
            $table->boolean('required')->default(false);
            $table->json('validation_rules')->nullable(); // {min, max, regex, pattern, file_types, file_max_size}
            $table->json('conditional_logic')->nullable(); // {show_if: {question_id, operator, value}}
            $table->json('flagging_rules')->nullable(); // {flag_type: soft|hard, conditions: [...]}
            $table->text('doctor_notes')->nullable(); // Notes visible to doctor
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_questions');
    }
};



