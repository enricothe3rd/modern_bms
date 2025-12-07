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
        Schema::create('user_department_status_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_department_assignment_id')->constrained('user_department_assignments', 'id', 'udsa_uda_fk')->onDelete('cascade');
            $table->foreignId('review_status_id')->constrained('review_statuses', 'id', 'udsa_rs_fk')->onDelete('cascade');
            $table->boolean('can_approve')->default(true);
            $table->boolean('can_reject')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['user_department_assignment_id', 'review_status_id'], 'udsa_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_department_status_assignments');
    }
};
