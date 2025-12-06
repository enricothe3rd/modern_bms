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
        Schema::table('department_expense_type_allocations', function (Blueprint $table) {
            $table->year('year')->default(date('Y'))->after('expense_type_id');
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_expense_type_allocations', function (Blueprint $table) {
            $table->dropIndex(['year']);
            $table->dropColumn('year');
        });
    }
};
