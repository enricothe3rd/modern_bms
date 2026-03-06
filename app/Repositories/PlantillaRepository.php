<?php

namespace App\Repositories;

use App\Models\Plantilla;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PlantillaRepository
{
    public function __construct(
        protected Plantilla $model
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with([
            'department',
            'fiscalYear',
            'items.movements.salaryScheduleFrom',
            'items.movements.salaryScheduleTo',
            'items.groups.movements.salarySchedule',
        ])->latest();

        if (!empty($filters['department_id'])) {
            $query->where('department_id', (int) $filters['department_id']);
        }

        if (!empty($filters['fiscal_year_id'])) {
            $query->where('fiscal_year_id', (int) $filters['fiscal_year_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('group_name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Plantilla
    {
        return $this->model->create($data);
    }

    public function update(Plantilla $record, array $data): bool
    {
        return $record->update($data);
    }

    public function delete(Plantilla $record): bool
    {
        return (bool) $record->delete();
    }

    public function loadFull(Plantilla $record): Plantilla
    {
        return $record->load([
            'department',
            'fiscalYear',
            'items.movements.salaryScheduleFrom',
            'items.movements.salaryScheduleTo',
            'items.groups.movements.salarySchedule',
        ]);
    }

    public function getByFiscalYear(int $fiscalYearId): Collection
    {
        return $this->model->with([
            'department',
            'fiscalYear',
            'items.movements.salaryScheduleFrom',
            'items.movements.salaryScheduleTo',
            'items.groups.movements.salarySchedule',
        ])
            ->where('fiscal_year_id', $fiscalYearId)
            ->latest()
            ->get();
    }
}
