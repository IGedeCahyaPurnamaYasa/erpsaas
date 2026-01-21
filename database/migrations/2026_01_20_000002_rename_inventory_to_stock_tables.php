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
        // Rename inventories table to stocks
        Schema::rename('inventories', 'stocks');

        // Update foreign key in stock_transactions table
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->renameColumn('inventory_id', 'stock_id');
        });

        // Rename inventory_transactions table to stock_transactions
        Schema::rename('inventory_transactions', 'stock_transactions');

        // Recreate foreign key with new table and column names
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreign('stock_id')->references('id')->on('stocks')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the foreign key changes
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['stock_id']);
        });

        // Rename tables back
        Schema::rename('stock_transactions', 'inventory_transactions');
        Schema::rename('stocks', 'inventories');

        // Restore original foreign key
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->renameColumn('stock_id', 'inventory_id');
            $table->foreign('inventory_id')->references('id')->on('inventories')->cascadeOnDelete();
        });
    }
};