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
        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Pharmacy owner/admin user ID');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address');
            $table->string('postcode')->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->comment('Geo-coordinates for future use');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Geo-coordinates for future use');
            $table->boolean('is_priority')->default(false)->comment('Marked as "My Pharmacy" (priority) by admin');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->index('status');
            $table->index('is_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacies');
    }
};
