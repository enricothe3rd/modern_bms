<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main account categories
        \App\Models\Account::create([
            'code' => '1000',
            'description' => 'Assets'
        ]);

        \App\Models\Account::create([
            'code' => '1100',
            'description' => 'Current Assets'
        ]);

        \App\Models\Account::create([
            'code' => '1110',
            'description' => 'Cash and Cash Equivalents'
        ]);

        \App\Models\Account::create([
            'code' => '1120',
            'description' => 'Accounts Receivable'
        ]);

        \App\Models\Account::create([
            'code' => '1200',
            'description' => 'Fixed Assets'
        ]);

        \App\Models\Account::create([
            'code' => '2000',
            'description' => 'Liabilities'
        ]);

        \App\Models\Account::create([
            'code' => '2100',
            'description' => 'Current Liabilities'
        ]);

        \App\Models\Account::create([
            'code' => '2110',
            'description' => 'Accounts Payable'
        ]);

        \App\Models\Account::create([
            'code' => '2200',
            'description' => 'Long-term Liabilities'
        ]);

        \App\Models\Account::create([
            'code' => '3000',
            'description' => 'Equity'
        ]);

        \App\Models\Account::create([
            'code' => '4000',
            'description' => 'Revenue'
        ]);

        \App\Models\Account::create([
            'code' => '4100',
            'description' => 'Sales Revenue'
        ]);

        \App\Models\Account::create([
            'code' => '4200',
            'description' => 'Service Revenue'
        ]);

        \App\Models\Account::create([
            'code' => '5000',
            'description' => 'Expenses'
        ]);

        \App\Models\Account::create([
            'code' => '5100',
            'description' => 'Operating Expenses'
        ]);

        \App\Models\Account::create([
            'code' => '5200',
            'description' => 'Administrative Expenses'
        ]);
    }
}
