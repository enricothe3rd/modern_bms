<?php

namespace App\Services;

use App\Models\Department;
use App\Models\FiscalYear;
use App\Models\Plantilla;
use App\Models\SalarySchedule;
use App\Repositories\PlantillaRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PlantillaService
{
    public function __construct(
        protected PlantillaRepository $repository
    ) {}

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getDepartments()
    {
        return Department::orderBy('name')->get();
    }

    public function getFiscalYears()
    {
        return FiscalYear::orderBy('year', 'desc')->get();
    }

    public function getCurrentFiscalYear(): ?FiscalYear
    {
        return FiscalYear::where('is_current', true)->first();
    }

    public function getSalarySchedules()
    {
        return SalarySchedule::with('cells')->orderByDesc('effective_date')->get();
    }

    public function create(array $data): Plantilla
    {
        return DB::transaction(function () use ($data) {
            $items = array_values($data['items'] ?? []);
            unset($data['items']);

            $processed = $this->processItems($items);
            $data['grand_total'] = $processed['grand_total'];

            $record = $this->repository->create($data);
            $this->syncItems($record, $processed['items']);

            return $this->repository->loadFull($record);
        });
    }

    public function update(Plantilla $record, array $data): Plantilla
    {
        return DB::transaction(function () use ($record, $data) {
            $items = array_values($data['items'] ?? []);
            unset($data['items']);

            $processed = $this->processItems($items);
            $data['grand_total'] = $processed['grand_total'];

            $this->repository->update($record, $data);
            $record->items()->delete();
            $this->syncItems($record, $processed['items']);

            return $this->repository->loadFull($record->fresh());
        });
    }

    public function delete(Plantilla $record): bool
    {
        return $this->repository->delete($record);
    }

    public function loadFull(Plantilla $record): Plantilla
    {
        return $this->repository->loadFull($record);
    }

    public function generateSinglePdf(Plantilla $record)
    {
        $record = $this->repository->loadFull($record);
        return PdfService::make()
            ->configure(['paper' => 'a4', 'orientation' => 'landscape', 'font_size' => 10])
            ->template('pdf.plantilla', ['record' => $record])
            ->inline('plantilla-' . $record->id . '.pdf');
    }

    public function generateFiscalYearPdf(FiscalYear $fiscalYear)
    {
        $records = $this->repository->getByFiscalYear($fiscalYear->id);
        return PdfService::make()
            ->configure(['paper' => 'a4', 'orientation' => 'landscape', 'font_size' => 9])
            ->template('pdf.plantilla-by-fiscal-year', ['fiscalYear' => $fiscalYear, 'records' => $records])
            ->inline('plantilla-fy-' . $fiscalYear->year . '.pdf');
    }

    private function processItems(array $items): array
    {
        $scheduleMaps = $this->salaryScheduleMaps();
        $grandTotal = 0;
        $processed = [];

        foreach ($items as $index => $item) {
            $old = (int) ($item['old_count'] ?? 0);
            $new = (int) ($item['new_count'] ?? 0);
            $processedMovements = [];
            $processedGroups = [];
            $lineTotal = 0;
            $legacyMoveIndex = 0;
            $groupSort = 0;
            $toTotal = 0;
            $fromTotal = 0;

            if (($item['to_groups'] ?? null) || ($item['from_groups'] ?? null)) {
                foreach (['to', 'from'] as $groupType) {
                    $groups = array_values($item[$groupType . '_groups'] ?? []);
                    foreach ($groups as $group) {
                        $groupTotal = 0;
                        $groupMovements = [];

                        foreach (array_values($group['movements'] ?? []) as $movementIndex => $movement) {
                            $scheduleId = !empty($movement['salary_schedule_id']) ? (int) $movement['salary_schedule_id'] : null;
                            [$grade, $step] = $this->parseGradeStep($movement['salary_cell'] ?? null);
                            $amount = $this->lookupAmount($scheduleMaps, $scheduleId, $grade, $step);
                            $movementTotal = ($scheduleId && $grade && $step)
                                ? round(($amount ?? 0), 2)
                                : 0.0;
                            $groupTotal += $movementTotal;

                            $groupMovements[] = [
                                'label' => $movement['label'] ?? null,
                                'salary_schedule_id' => $scheduleId,
                                'salary_grade' => $grade,
                                'salary_step' => $step,
                                'salary_amount' => $amount,
                                'movement_total' => $movementTotal,
                                'sort_order' => $movementIndex,
                            ];
                        }

                        $groupTotal = round($groupTotal, 2);
                        if ($groupType === 'to') {
                            $toTotal += $groupTotal;
                        } else {
                            $fromTotal += $groupTotal;
                        }

                        $processedGroups[] = [
                            'group_type' => $groupType,
                            'group_label' => $group['group_label'] ?? null,
                            'group_total' => $groupTotal,
                            'sort_order' => $groupSort++,
                            'movements' => $groupMovements,
                        ];
                    }
                }

                $lineTotal = round(abs($toTotal - $fromTotal), 2);
            } else {
                // Legacy payload fallback.
                foreach (array_values($item['movements'] ?? []) as $movement) {
                    $fromScheduleId = !empty($movement['salary_schedule_from_id']) ? (int) $movement['salary_schedule_from_id'] : null;
                    $toScheduleId = !empty($movement['salary_schedule_to_id']) ? (int) $movement['salary_schedule_to_id'] : null;
                    [$fromGrade, $fromStep] = $this->parseGradeStep($movement['salary_from'] ?? null);
                    [$toGrade, $toStep] = $this->parseGradeStep($movement['salary_to'] ?? null);

                    $fromAmount = $this->lookupAmount($scheduleMaps, $fromScheduleId, $fromGrade, $fromStep);
                    $toAmount = $this->lookupAmount($scheduleMaps, $toScheduleId, $toGrade, $toStep);
                    $movementTotal = (($fromScheduleId && $toScheduleId && $fromGrade && $fromStep && $toGrade && $toStep)
                        ? round(($new * ($toAmount ?? 0)) - ($old * ($fromAmount ?? 0)), 2)
                        : 0.0);
                    $lineTotal += $movementTotal;

                    $processedMovements[] = [
                        'label' => $movement['label'] ?? null,
                        'salary_schedule_from_id' => $fromScheduleId,
                        'salary_schedule_to_id' => $toScheduleId,
                        'salary_from_grade' => $fromGrade,
                        'salary_from_step' => $fromStep,
                        'salary_to_grade' => $toGrade,
                        'salary_to_step' => $toStep,
                        'salary_from_amount' => $fromAmount,
                        'salary_to_amount' => $toAmount,
                        'movement_total' => $movementTotal,
                        'sort_order' => $legacyMoveIndex++,
                    ];
                }
                $lineTotal = round($lineTotal, 2);
            }

            $grandTotal += $lineTotal;

            $processed[] = [
                'employee_name' => $item['employee_name'],
                'position' => $item['position'],
                'old_count' => $old,
                'new_count' => $new,
                'salary_schedule_id' => null,
                'salary_schedule_from_id' => null,
                'salary_schedule_to_id' => null,
                'salary_from_grade' => null,
                'salary_from_step' => null,
                'salary_to_grade' => null,
                'salary_to_step' => null,
                'salary_from_amount' => null,
                'salary_to_amount' => null,
                'line_total' => $lineTotal,
                'sort_order' => $index,
                'movements' => $processedMovements,
                'groups' => $processedGroups,
            ];
        }

        return ['items' => $processed, 'grand_total' => round($grandTotal, 2)];
    }

    private function syncItems(Plantilla $record, array $items): void
    {
        foreach ($items as $item) {
            $movements = $item['movements'] ?? [];
            $groups = $item['groups'] ?? [];
            unset($item['movements']);
            unset($item['groups']);

            $plantillaItem = $record->items()->create($item);
            foreach ($movements as $movement) {
                $plantillaItem->movements()->create($movement);
            }
            foreach ($groups as $group) {
                $groupMovements = $group['movements'] ?? [];
                unset($group['movements']);
                $plantillaGroup = $plantillaItem->groups()->create($group);
                foreach ($groupMovements as $movement) {
                    $plantillaGroup->movements()->create($movement);
                }
            }
        }
    }

    private function salaryScheduleMaps(): array
    {
        $maps = [];
        $schedules = SalarySchedule::with('cells')->get();
        foreach ($schedules as $schedule) {
            $maps[$schedule->id] = [];
            foreach ($schedule->cells as $cell) {
                $maps[$schedule->id][$cell->salary_grade][$cell->step_no] = (float) $cell->amount;
            }
        }
        return $maps;
    }

    private function parseGradeStep(?string $value): array
    {
        if (!$value || !str_contains($value, '-')) {
            return [null, null];
        }
        [$grade, $step] = explode('-', $value, 2);
        return [(int) $grade ?: null, (int) $step ?: null];
    }

    private function lookupAmount(array $maps, ?int $scheduleId, ?int $grade, ?int $step): ?float
    {
        if (!$scheduleId || !$grade || !$step) {
            return null;
        }
        return $maps[$scheduleId][$grade][$step] ?? null;
    }
}
