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
        Schema::create('budget_realignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_realignment_id')->constrained('budget_realignments')->onDelete('cascade');
            $table->enum('type', ['from', 'to']);
            $table->foreignId('department_expense_type_allocation_id')
                  ->constrained('department_expense_type_allocations')
                  ->name('budget_realignment_items_allocation_fk');
            $table->decimal('amount', 15, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['budget_realignment_id', 'type']);
            $table->index('department_expense_type_allocation_id', 'budget_realignment_items_allocation_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_realignment_items');
    }
};
