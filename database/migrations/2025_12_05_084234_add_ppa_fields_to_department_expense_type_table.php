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
        Schema::table('department_expense_type', function (Blueprint $table) {
            $table->string('ppa_code')->nullable()->after('expense_type_id');
            $table->date('date_issued')->nullable()->after('ppa_code');
            $table->enum('status', ['saved', 'released'])->default('saved')->after('date_issued');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_expense_type', function (Blueprint $table) {
            $table->dropColumn(['ppa_code', 'date_issued', 'status']);
        });
    }
};