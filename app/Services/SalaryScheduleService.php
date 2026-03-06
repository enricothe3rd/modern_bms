<?php

namespace App\Services;

use App\Models\FiscalYear;
use App\Models\SalarySchedule;
use App\Repositories\SalaryScheduleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SalaryScheduleService
{
    public function __construct(
        protected SalaryScheduleRepository $repository
    ) {}

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getFiscalYears()
    {
        return FiscalYear::orderBy('year', 'desc')->get();
    }

    public function getCurrentFiscalYear(): ?FiscalYear
    {
        return FiscalYear::where('is_current', true)->first();
    }

    public function create(array $data): SalarySchedule
    {
        return DB::transaction(function () use ($data) {
            $cells = $data['cells'] ?? [];
            unset($data['cells']);

            $record = $this->repository->create($data);
            $this->syncCells($record, $cells);

            return $this->repository->loadFull($record);
        });
    }

    public function update(SalarySchedule $record, array $data): SalarySchedule
    {
        return DB::transaction(function () use ($record, $data) {
            $cells = $data['cells'] ?? [];
            unset($data['cells']);

            $this->repository->update($record, $data);
            $record->cells()->delete();
            $this->syncCells($record, $cells);

            return $this->repository->loadFull($record->fresh());
        });
    }

    public function delete(SalarySchedule $record): bool
    {
        return $this->repository->delete($record);
    }

    public function loadFull(SalarySchedule $record): SalarySchedule
    {
        return $this->repository->loadFull($record);
    }

    public function gridData(SalarySchedule $record): array
    {
        $record = $this->repository->loadFull($record);
        $grid = [];
        foreach ($record->cells as $cell) {
            $grid[$cell->salary_grade][$cell->step_no] = $cell->amount;
        }
        return $grid;
    }

    public function defaultGrid(int $maxGrade = 33, int $totalSteps = 8): array
    {
        $grid = [];
        for ($g = 1; $g <= $maxGrade; $g++) {
            for ($s = 1; $s <= $totalSteps; $s++) {
                $grid[$g][$s] = null;
            }
        }
        return $grid;
    }

    public function generateSinglePdf(SalarySchedule $record)
    {
        $record = $this->repository->loadFull($record);
        $grid = $this->gridData($record);

        return PdfService::make()
            ->configure([
                'paper' => 'a4',
                'orientation' => 'landscape',
                'font_size' => 9,
                'margin' => ['top' => 10, 'right' => 8, 'bottom' => 10, 'left' => 8],
            ])
            ->template('pdf.salary-schedule', ['record' => $record, 'grid' => $grid])
            ->inline('salary-schedule-' . $record->id . '.pdf');
    }

    public function generateFiscalYearPdf(FiscalYear $fiscalYear)
    {
        $records = $this->repository->getByFiscalYear($fiscalYear->id);

        return PdfService::make()
            ->configure([
                'paper' => 'a4',
                'orientation' => 'landscape',
                'font_size' => 8,
                'margin' => ['top' => 10, 'right' => 8, 'bottom' => 10, 'left' => 8],
            ])
            ->template('pdf.salary-schedule-by-fiscal-year', ['fiscalYear' => $fiscalYear, 'records' => $records])
            ->inline('salary-schedules-fy-' . $fiscalYear->year . '.pdf');
    }

    private function syncCells(SalarySchedule $record, array $cells): void
    {
        foreach ($cells as $grade => $steps) {
            foreach ((array) $steps as $step => $amount) {
                if ($amount === null || $amount === '') {
                    continue;
                }
                $record->cells()->create([
                    'salary_grade' => (int) $grade,
                    'step_no' => (int) $step,
                    'amount' => (float) $amount,
                ]);
            }
        }
    }
}
