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
            $table->foreignId('fiscal_year_id')->after('budget_year')->nullable()->constrained('fiscal_years');
            $table->index(['fiscal_year_id', 'status']);
            $table->index(['fiscal_year_id', 'created_at']);
        });

        // Update existing records to use the current fiscal year
        $currentFiscalYear = \App\Models\FiscalYear::where('is_current', true)->first();
        if ($currentFiscalYear) {
            \DB::table('supplemental_budgets')
                ->whereNull('fiscal_year_id')
                ->update(['fiscal_year_id' => $currentFiscalYear->id]);
        }

        // Make fiscal_year_id required after updating existing records
        Schema::table('supplemental_budgets', function (Blueprint $table) {
            $table->foreignId('fiscal_year_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplemental_budgets', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropIndex(['fiscal_year_id', 'status']);
            $table->dropIndex(['fiscal_year_id', 'created_at']);
            $table->dropColumn('fiscal_year_id');
        });
    }
};
