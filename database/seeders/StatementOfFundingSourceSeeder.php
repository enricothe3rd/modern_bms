<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use App\Models\StatementOfFundingSource;
use Illuminate\Database\Seeder;

class StatementOfFundingSourceSeeder extends Seeder
{
    public function run(): void
    {
        $fiscalYears = $this->ensureFiscalYears();
        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $statement = StatementOfFundingSource::create([
                'fiscal_year_id' => $fiscalYears->random()->id,
                'title' => 'Funding Sources ' . $faker->unique()->numerify('###'),
                'remarks' => $faker->optional(0.7)->sentence(),
            ]);

            $categoryCount = random_int(2, 5);
            $categories = [];

            for ($c = 0; $c < $categoryCount; $c++) {
                $category = $statement->categories()->create([
                    'category_number' => ($c + 1) . '.0',
                    'category_name' => $faker->randomElement([
                        'Internal Revenue Allotment',
                        'Local Sources',
                        'External Grants',
                        'Borrowings',
                        'Trust Receipts',
                        'Other Income Sources',
                    ]) . ' ' . chr(65 + $c),
                    'amount' => $faker->optional(0.8)->randomFloat(2, 10000, 2000000),
                    'sort_order' => $c,
                ]);

                $categories[] = $category;
            }

            $itemSort = 0;
            foreach ($categories as $category) {
                $itemCount = random_int(2, 6);
                for ($j = 0; $j < $itemCount; $j++) {
                    $category->items()->create([
                        'particulars' => $faker->randomElement([
                            'Share from national tax allocation',
                            'Real property tax collection',
                            'Business tax collection',
                            'Grant proceeds',
                            'Loan drawdown',
                            'Service income',
                            'Miscellaneous receipts',
                        ]) . ' - ' . $faker->words(2, true),
                        'account_classification' => $faker->optional(0.6)->bothify('AC-##-??'),
                        'amount' => $faker->optional(0.85)->randomFloat(2, 1000, 800000),
                        'sort_order' => $itemSort++,
                    ]);
                }
            }
        }
    }

    private function ensureFiscalYears()
    {
        if (FiscalYear::count() === 0) {
            for ($year = (int) date('Y') - 2; $year <= (int) date('Y') + 2; $year++) {
                FiscalYear::firstOrCreate(['year' => $year], FiscalYear::generateForYear($year));
            }
        }

        return FiscalYear::query()->get();
    }
}
