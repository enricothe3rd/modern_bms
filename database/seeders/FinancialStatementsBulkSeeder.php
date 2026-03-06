<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FinancialStatementsBulkSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StatementOfIndebtednessSeeder::class,
            StatementOfFundingSourceSeeder::class,
            StatementOfStatutoryObligationSeeder::class,
        ]);
    }
}
