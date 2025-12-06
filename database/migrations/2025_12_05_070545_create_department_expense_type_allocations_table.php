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
        Schema::create('department_expense_type_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('expense_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('sub_account_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
            
            // Note: At least one of account_id or sub_account_id should be provided (enforced in validation)
            
            // Unique constraint to prevent duplicate allocations
            $table->unique(['department_id', 'expense_type_id', 'account_id', 'sub_account_id'], 'dept_expense_account_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_expense_type_allocations');
    }
};