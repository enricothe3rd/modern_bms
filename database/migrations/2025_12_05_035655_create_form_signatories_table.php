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
        Schema::create('form_signatories', function (Blueprint $table) {
            $table->id();
            $table->string('form_name');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('signatory_id')->constrained('users')->onDelete('cascade');
            $table->integer('order')->default(1); // Order of signature
            $table->timestamps();
            
            // Ensure unique combination of form, department, and signatory
            $table->unique(['form_name', 'department_id', 'signatory_id'], 'unique_form_dept_signatory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_signatories');
    }
};