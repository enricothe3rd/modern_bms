<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplemental_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->year('budget_year');
            $table->string('supplemental_group'); // e.g., "Supplemental Budget 1", "Mid-Year Adjustment", etc.
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('submission_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['budget_year', 'supplemental_group']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplemental_budgets');
    }
};