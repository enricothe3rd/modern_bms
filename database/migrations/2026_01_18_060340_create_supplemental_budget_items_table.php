<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplemental_budget_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplemental_budget_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('expense_type_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('sub_account_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->text('justification')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['supplemental_budget_id', 'department_id'], 'sb_items_budget_dept_idx');
            $table->index(['department_id', 'expense_type_id'], 'sb_items_dept_expense_idx');
            
            $table->foreign('supplemental_budget_id')->references('id')->on('supplemental_budgets')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('expense_type_id')->references('id')->on('expense_types');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('sub_account_id')->references('id')->on('sub_accounts');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplemental_budget_items');
    }
};