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
        if (Schema::hasTable('questionnaire_submissions')) {
            return;
        }

        Schema::create('questionnaire_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('questionnaire_id');
            
            // Delivery choice: 'delivery' or 'pickup'
            $table->enum('delivery_type', ['delivery', 'pickup'])->nullable();
            
            // Delivery address (if delivery is selected)
            $table->unsignedBigInteger('delivery_address_id')->nullable();
            $table->string('delivery_postcode')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state')->nullable();
            $table->text('delivery_address')->nullable();
            
            // Pharmacy selection (if pickup is selected)
            $table->unsignedBigInteger('selected_pharmacy_id')->nullable();
            
            // Selected medicines (JSON array of medicine IDs with type: {medicine_id, type: 'generic'|'branded'|'premium'})
            $table->json('selected_medicines')->nullable();
            
            // Status tracking
            $table->enum('status', ['pending', 'medicine_selected', 'completed'])->default('pending');
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires')->onDelete('cascade');
            $table->foreign('delivery_address_id')->references('id')->on('user_address')->onDelete('set null');
            $table->foreign('selected_pharmacy_id')->references('id')->on('pharmacy')->onDelete('set null');
            
            // Indexes
            $table->index(['user_id', 'category_id', 'questionnaire_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_submissions');
    }
};
