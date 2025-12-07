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
        Schema::create('review_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Submitted", "Approved", "Rejected"
            $table->string('code')->unique(); // e.g., "submitted", "approved", "rejected"
            $table->string('description')->nullable();
            $table->string('color')->default('#6B7280'); // Hex color for UI display
            $table->integer('order')->default(0); // For sorting workflow steps
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_statuses');
    }
};
