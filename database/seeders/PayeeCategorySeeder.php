<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PayeeCategory;

class PayeeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Supplier',
                'description' => 'Vendors and suppliers providing goods and services',
            ],
            [
                'name' => 'Individual',
                'description' => 'Individual persons receiving payments',
            ],
            [
                'name' => 'Contractor',
                'description' => 'Contractors and service providers',
            ],
            [
                'name' => 'Others',
                'description' => 'Other types of payees not classified above',
            ],
        ];

        foreach ($categories as $category) {
            PayeeCategory::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }

        $this->command->info('Payee categories seeded successfully!');
    }
}
