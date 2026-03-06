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
        Schema::table('supplemental_budgets', function (Blueprint $table) {
            $table->dropColumn('budget_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplemental_budgets', function (Blueprint $table) {
            $table->integer('budget_year')->after('description');
        });
    }
};
