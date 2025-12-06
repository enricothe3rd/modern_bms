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
        Schema::create('obligation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('obr_number')->unique();
            $table->foreignId('department_id')->constrained()->onDelete('cascade'); // Responsibility Center
            $table->foreignId('claimant_payee_id')->constrained()->onDelete('cascade');
            $table->date('obligation_date');
            $table->text('particulars');
            $table->text('optional_field_1')->nullable();
            $table->text('optional_field_2')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligation_requests');
    }
};
