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
        Schema::table('offerings', function (Blueprint $table) {
            $table->foreignId('stock_keeping_unit_id')->nullable()->constrained('stock_keeping_units')->nullOnDelete();
            $table->char('stock_keeping_unit_number', 4);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->dropForeign('offerings_stock_keeping_unit_id_foreign');
            $table->dropColumn('stock_keeping_unit_id');
            $table->dropColumn('stock_keeping_unit_number');
        });
    }
};
