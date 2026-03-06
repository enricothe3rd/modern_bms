<?php

namespace App\Repositories;

use App\Models\SalarySchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SalaryScheduleRepository
{
    public function __construct(
        protected SalarySchedule $model
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('fiscalYear')->latest();

        if (!empty($filters['fiscal_year_id'])) {
            $query->where('fiscal_year_id', (int) $filters['fiscal_year_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $data): SalarySchedule
    {
        return $this->model->create($data);
    }

    public function update(SalarySchedule $record, array $data): bool
    {
        return $record->update($data);
    }

    public function delete(SalarySchedule $record): bool
    {
        return (bool) $record->delete();
    }

    public function loadFull(SalarySchedule $record): SalarySchedule
    {
        return $record->load(['fiscalYear', 'cells']);
    }

    public function getByFiscalYear(int $fiscalYearId): Collection
    {
        return $this->model->with(['fiscalYear', 'cells'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->latest()
            ->get();
    }
}
