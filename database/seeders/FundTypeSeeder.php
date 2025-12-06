<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FundTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fundTypes = [
            'General Fund',
            'Special Education Fund',
            'Trust Fund',
            'Capital Projects Fund',
            'Debt Service Fund',
            'Enterprise Fund',
            'Internal Service Fund',
            'Pension Trust Fund',
            'Investment Trust Fund',
            'Private Purpose Trust Fund'
        ];

        foreach ($fundTypes as $description) {
            \App\Models\FundType::create([
                'description' => $description
            ]);
        }
    }
}
