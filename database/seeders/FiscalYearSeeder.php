<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FiscalYear;
use Carbon\Carbon;

class FiscalYearSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        
        // Create fiscal years from 2023 to current year + 2
        for ($year = 2023; $year <= $currentYear + 2; $year++) {
            FiscalYear::create([
                'year' => $year,
                'description' => "Fiscal Year {$year}",
                'start_date' => Carbon::create($year, 1, 1),
                'end_date' => Carbon::create($year, 12, 31),
                'is_active' => $year >= $currentYear - 1, // Active for last year, current year, and future years
                'is_current' => $year == $currentYear
            ]);
        }
    }
}