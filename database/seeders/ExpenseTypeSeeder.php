<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenseTypes = [
            [
                'description' => 'Personnel Services',
                'acronym' => 'PS'
            ],
            [
                'description' => 'Maintenance and Other Operating Expenses',
                'acronym' => 'MOOE'
            ],
            [
                'description' => 'Capital Outlay',
                'acronym' => 'CO'
            ],
            [
                'description' => 'Travel and Transportation',
                'acronym' => 'TT'
            ],
            [
                'description' => 'Office Equipment',
                'acronym' => 'OE'
            ],
            [
                'description' => 'Office Supplies',
                'acronym' => 'OS'
            ],
            [
                'description' => 'Utilities',
                'acronym' => 'UT'
            ],
            [
                'description' => 'Communication',
                'acronym' => 'COM'
            ],
            [
                'description' => 'Professional Services',
                'acronym' => 'PROF'
            ],
            [
                'description' => 'Training and Seminars',
                'acronym' => 'TS'
            ],
            [
                'description' => 'Repairs and Maintenance',
                'acronym' => 'RM'
            ],
            [
                'description' => 'Fuel and Lubricants',
                'acronym' => 'FL'
            ]
        ];

        foreach ($expenseTypes as $expenseType) {
            \App\Models\ExpenseType::create($expenseType);
        }
    }
}
