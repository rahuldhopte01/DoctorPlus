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
        Schema::create('cannaleo_prescription_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prescription_id')->nullable();
            $table->unsignedBigInteger('questionnaire_submission_id')->nullable();
            $table->timestamp('called_at');
            $table->json('request_payload')->nullable();
            $table->unsignedInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->string('external_order_id')->nullable();
            $table->json('products_snapshot');
            $table->decimal('total_medicine_cost', 10, 2)->nullable();
            $table->decimal('prescription_fee', 10, 2)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('prescription_id')->references('id')->on('prescription')->nullOnDelete();
            $table->foreign('questionnaire_submission_id')->references('id')->on('questionnaire_submissions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cannaleo_prescription_log');
    }
};
