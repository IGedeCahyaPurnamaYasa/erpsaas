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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('asset_type');
            $table->string('serial_number')->nullable();
            $table->string('tag_number')->nullable();
            $table->string('status')->default('active');
            $table->string('location')->nullable();
            $table->string('assigned_to')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->date('usage_date')->nullable();
            $table->decimal('value', 15, 2)->nullable();
            $table->foreignId('depreciation_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('expense_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['company_id', 'asset_type']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'location']);
            $table->index(['company_id', 'assigned_to']);
            $table->index(['company_id', 'acquisition_date']);
            
            // Unique constraints
            $table->unique(['company_id', 'serial_number']);
            $table->unique(['company_id', 'tag_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};