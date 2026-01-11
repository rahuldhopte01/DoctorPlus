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
     * Drops medicine_category table and restructures medicine and medicine_brands tables.
     */
    public function up(): void
    {
        // Drop foreign key constraint from medicine table for medicine_category_id if it exists
        if (Schema::hasColumn('medicine', 'medicine_category_id')) {
            // Try to find and drop the foreign key constraint
            try {
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'medicine' 
                    AND COLUMN_NAME = 'medicine_category_id' 
                    AND CONSTRAINT_NAME != 'PRIMARY'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                foreach ($constraints as $constraint) {
                    try {
                        DB::statement("ALTER TABLE medicine DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                    } catch (\Exception $e) {
                        // Constraint might not exist, continue
                    }
                }
            } catch (\Exception $e) {
                // If query fails, try using Schema builder
                try {
                    Schema::table('medicine', function (Blueprint $table) {
                        $table->dropForeign(['medicine_category_id']);
                    });
                } catch (\Exception $e2) {
                    // Foreign key might not exist, that's okay - we'll drop the column anyway
                }
            }
        }

        // Remove medicine_id from medicine_brands table FIRST (to avoid circular foreign key issues)
        if (Schema::hasColumn('medicine_brands', 'medicine_id')) {
            Schema::table('medicine_brands', function (Blueprint $table) {
                // Drop foreign key first
                $table->dropForeign(['medicine_id']);
            });
            Schema::table('medicine_brands', function (Blueprint $table) {
                $table->dropIndex(['medicine_id']);
                $table->dropColumn('medicine_id');
            });
        }

        // Remove columns from medicine table
        Schema::table('medicine', function (Blueprint $table) {
            // Remove medicine_category_id
            if (Schema::hasColumn('medicine', 'medicine_category_id')) {
                $table->dropColumn('medicine_category_id');
            }
            
            // Remove stock and pricing related columns
            if (Schema::hasColumn('medicine', 'stock_quantity')) {
                $table->dropColumn('stock_quantity');
            }
            if (Schema::hasColumn('medicine', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('medicine', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('medicine', 'works')) {
                $table->dropColumn('works');
            }
            if (Schema::hasColumn('medicine', 'number_of_medicine')) {
                $table->dropColumn('number_of_medicine');
            }
            if (Schema::hasColumn('medicine', 'price_pr_strip')) {
                $table->dropColumn('price_pr_strip');
            }
            if (Schema::hasColumn('medicine', 'incoming_stock')) {
                $table->dropColumn('incoming_stock');
            }
            if (Schema::hasColumn('medicine', 'use_stock')) {
                $table->dropColumn('use_stock');
            }
            if (Schema::hasColumn('medicine', 'total_stock')) {
                $table->dropColumn('total_stock');
            }
            if (Schema::hasColumn('medicine', 'prescription_required')) {
                $table->dropColumn('prescription_required');
            }
            if (Schema::hasColumn('medicine', 'meta_info')) {
                $table->dropColumn('meta_info');
            }
        });

        // Add brand_id to medicine table (after removing medicine_id from medicine_brands)
        if (!Schema::hasColumn('medicine', 'brand_id')) {
            Schema::table('medicine', function (Blueprint $table) {
                $table->unsignedBigInteger('brand_id')->nullable()->after('form');
                $table->foreign('brand_id')->references('id')->on('medicine_brands')->onDelete('set null');
                $table->index('brand_id');
            });
        }

        // Drop medicine_category table
        Schema::dropIfExists('medicine_category');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate medicine_category table
        Schema::create('medicine_category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        // Add medicine_id back to medicine_brands
        if (!Schema::hasColumn('medicine_brands', 'medicine_id')) {
            Schema::table('medicine_brands', function (Blueprint $table) {
                $table->unsignedBigInteger('medicine_id')->nullable()->after('id');
                $table->foreign('medicine_id')->references('id')->on('medicine')->onDelete('cascade');
                $table->index('medicine_id');
            });
        }

        // Remove brand_id from medicine table
        if (Schema::hasColumn('medicine', 'brand_id')) {
            Schema::table('medicine', function (Blueprint $table) {
                $table->dropForeign(['brand_id']);
                $table->dropIndex(['brand_id']);
                $table->dropColumn('brand_id');
            });
        }

        // Add columns back to medicine table (simplified - you may need to adjust types)
        Schema::table('medicine', function (Blueprint $table) {
            if (!Schema::hasColumn('medicine', 'medicine_category_id')) {
                $table->unsignedBigInteger('medicine_category_id')->after('form');
                $table->foreign('medicine_category_id')->references('id')->on('medicine_category')->onDelete('cascade');
            }
            if (!Schema::hasColumn('medicine', 'image')) {
                $table->string('image')->after('form');
            }
            if (!Schema::hasColumn('medicine', 'stock_quantity')) {
                $table->integer('stock_quantity')->nullable();
            }
            if (!Schema::hasColumn('medicine', 'price')) {
                $table->integer('price')->nullable();
            }
            if (!Schema::hasColumn('medicine', 'works')) {
                $table->text('works')->nullable();
            }
            if (!Schema::hasColumn('medicine', 'number_of_medicine')) {
                $table->integer('number_of_medicine')->nullable();
            }
            if (!Schema::hasColumn('medicine', 'price_pr_strip')) {
                $table->integer('price_pr_strip')->nullable();
            }
            if (!Schema::hasColumn('medicine', 'incoming_stock')) {
                $table->integer('incoming_stock')->nullable();
            }
            if (!Schema::hasColumn('medicine', 'use_stock')) {
                $table->integer('use_stock')->default(0);
            }
            if (!Schema::hasColumn('medicine', 'total_stock')) {
                $table->integer('total_stock')->nullable();
            }
            if (!Schema::hasColumn('medicine', 'prescription_required')) {
                $table->boolean('prescription_required')->default(0);
            }
            if (!Schema::hasColumn('medicine', 'meta_info')) {
                $table->text('meta_info')->nullable();
            }
        });
    }
};
