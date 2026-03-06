<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use App\Models\StatementOfIndebtedness;
use Illuminate\Database\Seeder;

class StatementOfIndebtednessSeeder extends Seeder
{
    public function run(): void
    {
        $fiscalYears = $this->ensureFiscalYears();
        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $principal = $faker->randomFloat(2, 50000, 5000000);
            $prevPrincipal = $faker->randomFloat(2, 0, $principal * 0.40);
            $prevInterest = $faker->randomFloat(2, 0, $principal * 0.10);
            $duePrincipal = $faker->randomFloat(2, 0, max($principal - $prevPrincipal, 0));
            $dueInterest = $faker->randomFloat(2, 0, $principal * 0.08);

            StatementOfIndebtedness::create([
                'fiscal_year_id' => $fiscalYears->random()->id,
                'creditor' => $faker->company(),
                'date_contracted' => $faker->dateTimeBetween('-8 years', 'now')->format('Y-m-d'),
                'term_maturity' => $faker->randomElement(['1 year', '3 years', '5 years', '10 years', 'Until 2030', 'Until 2035']),
                'principal_amount' => $principal,
                'purpose' => $faker->randomElement([
                    'Infrastructure development financing',
                    'Capital outlay support',
                    'Equipment acquisition',
                    'Public services expansion',
                    'Emergency rehabilitation fund',
                    'Program implementation support',
                ]),
                'prev_principal' => $prevPrincipal,
                'prev_interest' => $prevInterest,
                'prev_total' => round($prevPrincipal + $prevInterest, 2),
                'due_principal' => $duePrincipal,
                'due_interest' => $dueInterest,
                'due_total' => round($duePrincipal + $dueInterest, 2),
                'balance' => round($principal - $duePrincipal, 2),
            ]);
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
