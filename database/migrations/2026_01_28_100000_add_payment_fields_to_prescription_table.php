<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds payment-related fields to the prescription table for Stripe integration.
     */
    public function up(): void
    {
        Schema::table('prescription', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription', 'payment_amount')) {
                $table->decimal('payment_amount', 10, 2)->nullable()->after('status');
            }
            if (!Schema::hasColumn('prescription', 'payment_status')) {
                $table->boolean('payment_status')->default(0)->after('payment_amount');
            }
            if (!Schema::hasColumn('prescription', 'payment_token')) {
                $table->string('payment_token')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('prescription', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('payment_token');
            }
            if (!Schema::hasColumn('prescription', 'payment_date')) {
                $table->dateTime('payment_date')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('prescription', 'stripe_session_id')) {
                $table->string('stripe_session_id')->nullable()->after('payment_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription', function (Blueprint $table) {
            $columns = ['stripe_session_id', 'payment_date', 'payment_method', 'payment_token', 'payment_status', 'payment_amount'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('prescription', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
