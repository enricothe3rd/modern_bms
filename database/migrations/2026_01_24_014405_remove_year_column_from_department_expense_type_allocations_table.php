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
            // Drop the year column and its index
            $table->dropIndex(['year']);
            $table->dropColumn('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_expense_type_allocations', function (Blueprint $table) {
            // Re-add the year column
            $table->year('year')->after('fiscal_year_id');
            $table->index('year');
        });
        
        // Repopulate year values from fiscal_years
        $this->repopulateYearValues();
    }

    /**
     * Repopulate year values from fiscal_years table
     */
    private function repopulateYearValues()
    {
        $allocations = DB::table('department_expense_type_allocations')
            ->join('fiscal_years', 'department_expense_type_allocations.fiscal_year_id', '=', 'fiscal_years.id')
            ->select('department_expense_type_allocations.id', 'fiscal_years.year')
            ->get();

        foreach ($allocations as $allocation) {
            DB::table('department_expense_type_allocations')
                ->where('id', $allocation->id)
                ->update(['year' => $allocation->year]);
        }
    }
};