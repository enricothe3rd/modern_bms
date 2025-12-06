<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some accounts to create sub-accounts for
        $assetsAccount = \App\Models\Account::where('code', '1000')->first();
        $currentAssetsAccount = \App\Models\Account::where('code', '1100')->first();
        $liabilitiesAccount = \App\Models\Account::where('code', '2000')->first();
        $revenueAccount = \App\Models\Account::where('code', '4000')->first();
        $expensesAccount = \App\Models\Account::where('code', '5000')->first();

        if ($assetsAccount) {
            // Sub-accounts for Assets (1000)
            \App\Models\SubAccount::create([
                'code' => '1001',
                'description' => 'Petty Cash',
                'account_id' => $assetsAccount->id
            ]);

            \App\Models\SubAccount::create([
                'code' => '1002',
                'description' => 'Bank Account - Main',
                'account_id' => $assetsAccount->id
            ]);
        }

        if ($currentAssetsAccount) {
            // Sub-accounts for Current Assets (1100)
            \App\Models\SubAccount::create([
                'code' => '1101',
                'description' => 'Cash on Hand',
                'account_id' => $currentAssetsAccount->id
            ]);

            \App\Models\SubAccount::create([
                'code' => '1102',
                'description' => 'Checking Account',
                'account_id' => $currentAssetsAccount->id
            ]);

            \App\Models\SubAccount::create([
                'code' => '1103',
                'description' => 'Savings Account',
                'account_id' => $currentAssetsAccount->id
            ]);
        }

        if ($liabilitiesAccount) {
            // Sub-accounts for Liabilities (2000)
            \App\Models\SubAccount::create([
                'code' => '2001',
                'description' => 'Accounts Payable - Trade',
                'account_id' => $liabilitiesAccount->id
            ]);

            \App\Models\SubAccount::create([
                'code' => '2002',
                'description' => 'Accrued Expenses',
                'account_id' => $liabilitiesAccount->id
            ]);
        }

        if ($revenueAccount) {
            // Sub-accounts for Revenue (4000)
            \App\Models\SubAccount::create([
                'code' => '4001',
                'description' => 'Product Sales',
                'account_id' => $revenueAccount->id
            ]);

            \App\Models\SubAccount::create([
                'code' => '4002',
                'description' => 'Service Income',
                'account_id' => $revenueAccount->id
            ]);
        }

        if ($expensesAccount) {
            // Sub-accounts for Expenses (5000)
            \App\Models\SubAccount::create([
                'code' => '5001',
                'description' => 'Office Supplies',
                'account_id' => $expensesAccount->id
            ]);

            \App\Models\SubAccount::create([
                'code' => '5002',
                'description' => 'Utilities',
                'account_id' => $expensesAccount->id
            ]);

            \App\Models\SubAccount::create([
                'code' => '5003',
                'description' => 'Travel Expenses',
                'account_id' => $expensesAccount->id
            ]);
        }
    }
}
