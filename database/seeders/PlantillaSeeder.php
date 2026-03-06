<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\FiscalYear;
use App\Models\Plantilla;
use App\Models\SalarySchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlantillaSeeder extends Seeder
{
    private const TARGET_EMPLOYEES_PER_DEPARTMENT = 100;
    private const FISCAL_YEAR_COUNT = 3;

    public function run(): void
    {
        $departments = Department::query()->orderBy('name')->get();

        if ($departments->isEmpty()) {
            $this->command?->warn('No departments found. Run DepartmentSeeder first.');
            return;
        }

        $fiscalYears = $this->ensureFiscalYears();
        $schedules = $this->ensureSalarySchedules();

        if ($schedules->isEmpty()) {
            $this->command?->warn('No salary schedules with cells found. Run SalaryScheduleAnnexA2026Seeder first.');
            return;
        }

        $schedulePool = $schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'cells' => $schedule->cells
                    ->filter(fn ($cell) => $cell->amount !== null)
                    ->values()
                    ->map(fn ($cell) => [
                        'salary_grade' => (int) $cell->salary_grade,
                        'step_no' => (int) $cell->step_no,
                        'amount' => (float) $cell->amount,
                    ])->all(),
            ];
        })->filter(fn ($s) => !empty($s['cells']))->values();

        if ($schedulePool->isEmpty()) {
            $this->command?->warn('Salary schedules exist but no usable salary cells were found.');
            return;
        }

        $faker = fake();

        foreach ($departments as $department) {
            $yearSlice = $fiscalYears->take(min(self::FISCAL_YEAR_COUNT, max(1, $fiscalYears->count())));
            $distribution = $this->distributeCount(self::TARGET_EMPLOYEES_PER_DEPARTMENT, $yearSlice->count());

            foreach ($yearSlice as $idx => $fiscalYear) {
                $employeeCount = $distribution[$idx] ?? 0;
                if ($employeeCount <= 0) {
                    continue;
                }

                DB::transaction(function () use ($department, $fiscalYear, $employeeCount, $faker, $schedulePool) {
                    $plantilla = Plantilla::create([
                        'department_id' => $department->id,
                        'fiscal_year_id' => $fiscalYear->id,
                        'group_name' => 'Seeded Plantilla FY ' . $fiscalYear->year . ' - ' . $department->name,
                        'notes' => 'Auto-generated seeder data (' . $employeeCount . ' employees).',
                        'grand_total' => 0,
                    ]);

                    $grandTotal = 0.0;

                    for ($i = 0; $i < $employeeCount; $i++) {
                        $oldCount = $faker->numberBetween(0, 3);
                        $newCount = $faker->numberBetween(0, 3);

                        $item = $plantilla->items()->create([
                            'employee_name' => $faker->name(),
                            'position' => $faker->randomElement([
                                'Administrative Aide',
                                'Administrative Officer',
                                'Accountant',
                                'Budget Officer',
                                'Planning Officer',
                                'Clerk',
                                'Management and Audit Analyst',
                                'Engineer',
                                'Nurse',
                                'Staff Assistant',
                            ]),
                            'old_count' => $oldCount,
                            'new_count' => $newCount,
                            'line_total' => 0,
                            'sort_order' => $i,
                        ]);

                        $toGroupTotal = $this->seedGroup($item, 'to', 'Salary To Group', $schedulePool, $faker);
                        $fromGroupTotal = $this->seedGroup($item, 'from', 'Salary From Group', $schedulePool, $faker);
                        $lineTotal = round(abs($toGroupTotal - $fromGroupTotal), 2);

                        $item->update(['line_total' => $lineTotal]);
                        $grandTotal += $lineTotal;
                    }

                    $plantilla->update(['grand_total' => round($grandTotal, 2)]);
                });
            }
        }

        $this->command?->info('PlantillaSeeder completed: at least 100 employees per department across multiple fiscal years.');
    }

    private function seedGroup($item, string $type, string $label, Collection $schedulePool, $faker): float
    {
        $group = $item->groups()->create([
            'group_type' => $type,
            'group_label' => $label,
            'group_total' => 0,
            'sort_order' => $type === 'to' ? 0 : 1,
        ]);

        $movementCount = $faker->numberBetween(1, 3);
        $groupTotal = 0.0;

        for ($m = 0; $m < $movementCount; $m++) {
            $schedule = $schedulePool->random();
            $cell = collect($schedule['cells'])->random();
            $amount = round((float) $cell['amount'], 2);

            $group->movements()->create([
                'label' => $type === 'to'
                    ? $faker->randomElement(['Tranche A', 'Tranche B', 'Adjustment', 'Step Change'])
                    : $faker->randomElement(['Base', 'Current', 'Prior', 'Reference']),
                'salary_schedule_id' => $schedule['id'],
                'salary_grade' => $cell['salary_grade'],
                'salary_step' => $cell['step_no'],
                'salary_amount' => $amount,
                'movement_total' => $amount,
                'sort_order' => $m,
            ]);

            $groupTotal += $amount;
        }

        $groupTotal = round($groupTotal, 2);
        $group->update(['group_total' => $groupTotal]);

        return $groupTotal;
    }

    private function ensureFiscalYears(): Collection
    {
        if (FiscalYear::count() === 0) {
            $currentYear = (int) date('Y');
            for ($year = $currentYear - 2; $year <= $currentYear + 2; $year++) {
                FiscalYear::firstOrCreate(['year' => $year], FiscalYear::generateForYear($year));
            }
        }

        // Ensure enough years for distribution.
        if (FiscalYear::count() < self::FISCAL_YEAR_COUNT) {
            $maxYear = (int) (FiscalYear::max('year') ?: date('Y'));
            while (FiscalYear::count() < self::FISCAL_YEAR_COUNT) {
                $maxYear++;
                FiscalYear::firstOrCreate(['year' => $maxYear], FiscalYear::generateForYear($maxYear));
            }
        }

        return FiscalYear::query()->orderBy('year')->get();
    }

    private function ensureSalarySchedules(): Collection
    {
        $schedules = SalarySchedule::with('cells')->get();

        if ($schedules->isEmpty()) {
            $this->call(SalaryScheduleAnnexA2026Seeder::class);
            $schedules = SalarySchedule::with('cells')->get();
        }

        return $schedules;
    }

    private function distributeCount(int $total, int $buckets): array
    {
        $buckets = max(1, $buckets);
        $base = intdiv($total, $buckets);
        $remainder = $total % $buckets;
        $result = [];

        for ($i = 0; $i < $buckets; $i++) {
            $result[] = $base + ($i < $remainder ? 1 : 0);
        }

        return $result;
    }
}
