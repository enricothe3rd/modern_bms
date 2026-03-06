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
            // Add fiscal_year_id foreign key
            $table->foreignId('fiscal_year_id')->nullable()->after('expense_type_id')->constrained()->onDelete('cascade');
            
            // Add index for better performance
            $table->index('fiscal_year_id');
        });

        // Populate fiscal_year_id based on existing year values
        $this->populateFiscalYearIds();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_expense_type_allocations', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropIndex(['fiscal_year_id']);
            $table->dropColumn('fiscal_year_id');
        });
    }

    /**
     * Populate fiscal_year_id based on existing year values
     */
    private function populateFiscalYearIds()
    {
        // Get all allocations with their year values
        $allocations = DB::table('department_expense_type_allocations')->get();
        
        foreach ($allocations as $allocation) {
            // Find or create fiscal year for this year
            $fiscalYear = DB::table('fiscal_years')->where('year', $allocation->year)->first();
            
            if (!$fiscalYear) {
                // Create fiscal year if it doesn't exist
                $fiscalYearId = DB::table('fiscal_years')->insertGetId([
                    'year' => $allocation->year,
                    'description' => "Fiscal Year {$allocation->year}",
                    'start_date' => "{$allocation->year}-01-01",
                    'end_date' => "{$allocation->year}-12-31",
                    'is_active' => $allocation->year >= date('Y') - 1,
                    'is_current' => $allocation->year == date('Y'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $fiscalYearId = $fiscalYear->id;
            }
            
            // Update allocation with fiscal_year_id
            DB::table('department_expense_type_allocations')
                ->where('id', $allocation->id)
                ->update(['fiscal_year_id' => $fiscalYearId]);
        }
    }
};