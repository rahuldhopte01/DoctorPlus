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
        // Check if foreign keys already exist
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'notification' 
            AND CONSTRAINT_NAME = 'notification_pharmacy_id_foreign'
        ");
        
        if (empty($fkExists)) {
            Schema::table('notification', function (Blueprint $table) {
                $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade');
            });
        }
        
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'notification' 
            AND CONSTRAINT_NAME = 'notification_pharmacy_inventory_id_foreign'
        ");
        
        if (empty($fkExists)) {
            Schema::table('notification', function (Blueprint $table) {
                $table->foreign('pharmacy_inventory_id')->references('id')->on('pharmacy_inventory')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_id']);
            $table->dropForeign(['pharmacy_inventory_id']);
        });
    }
};
