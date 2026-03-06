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
        Schema::create('budget_realignments', function (Blueprint $table) {
            $table->id();
            $table->string('realignment_number')->unique();
            $table->text('description');
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['fiscal_year_id', 'status']);
            $table->index(['fiscal_year_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_realignments');
    }
};
