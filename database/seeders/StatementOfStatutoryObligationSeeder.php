<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use App\Models\StatementOfStatutoryObligation;
use Illuminate\Database\Seeder;

class StatementOfStatutoryObligationSeeder extends Seeder
{
    public function run(): void
    {
        $fiscalYears = $this->ensureFiscalYears();
        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $statement = StatementOfStatutoryObligation::create([
                'fiscal_year_id' => $fiscalYears->random()->id,
                'title' => 'Statutory Obligations ' . $faker->unique()->numerify('###'),
                'remarks' => $faker->optional(0.6)->sentence(),
            ]);

            $categoryCount = random_int(2, 5);
            $categories = [];
            for ($c = 0; $c < $categoryCount; $c++) {
                $categories[] = $statement->categories()->create([
                    'category_number' => ($c + 1) . '.0',
                    'category_name' => $faker->randomElement([
                        'Personal Services Related',
                        'Mandatory Contributions',
                        'Debt Service',
                        'Insurance Premiums',
                        'Statutory Reserve',
                        'Other Legal Obligations',
                    ]) . ' ' . chr(65 + $c),
                    'sort_order' => $c,
                ]);
            }

            $itemSort = 0;
            foreach ($categories as $category) {
                $itemCount = random_int(2, 6);
                for ($j = 0; $j < $itemCount; $j++) {
                    $category->items()->create([
                        'code' => $faker->optional(0.85)->bothify('SO-###-??'),
                        'description' => $faker->randomElement([
                            'Government share contribution',
                            'Mandatory remittance',
                            'Loan amortization',
                            'Interest payment',
                            'Statutory reserve allocation',
                            'Premium contribution',
                        ]) . ' ' . $faker->words(2, true),
                        'amount' => $faker->optional(0.9)->randomFloat(2, 1000, 600000),
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
