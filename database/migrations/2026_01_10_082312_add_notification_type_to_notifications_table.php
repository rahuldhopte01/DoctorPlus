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
        // Check if columns already exist (from partial migration)
        if (!Schema::hasColumn('notification', 'notification_type')) {
            Schema::table('notification', function (Blueprint $table) {
                $table->string('notification_type')->nullable()->comment('Type of notification: low_stock, etc.');
            });
        }
        
        if (!Schema::hasColumn('notification', 'pharmacy_id')) {
            Schema::table('notification', function (Blueprint $table) {
                $table->unsignedBigInteger('pharmacy_id')->nullable();
            });
        }
        
        if (!Schema::hasColumn('notification', 'pharmacy_inventory_id')) {
            Schema::table('notification', function (Blueprint $table) {
                $table->unsignedBigInteger('pharmacy_inventory_id')->nullable();
            });
        }
        
        // Foreign keys will be added in a separate migration after all tables are confirmed to exist
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropColumn(['notification_type', 'pharmacy_id', 'pharmacy_inventory_id']);
        });
    }
};
