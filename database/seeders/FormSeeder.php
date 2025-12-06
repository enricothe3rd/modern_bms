<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Form;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $forms = [
            [
                'name' => 'Budget Request Form',
                'description' => 'Form for requesting budget allocations',
                'is_active' => true
            ],
            [
                'name' => 'Purchase Order Form',
                'description' => 'Form for creating purchase orders',
                'is_active' => true
            ],
            [
                'name' => 'Expense Reimbursement Form',
                'description' => 'Form for expense reimbursement requests',
                'is_active' => true
            ],
            [
                'name' => 'Travel Authorization Form',
                'description' => 'Form for travel authorization requests',
                'is_active' => true
            ],
            [
                'name' => 'Equipment Request Form',
                'description' => 'Form for requesting equipment',
                'is_active' => true
            ]
        ];

        foreach ($forms as $form) {
            Form::create($form);
        }
    }
}