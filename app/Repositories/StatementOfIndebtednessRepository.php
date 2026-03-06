<?php

namespace App\Repositories;

use App\Models\StatementOfIndebtedness;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StatementOfIndebtednessRepository
{
    public function __construct(
        protected StatementOfIndebtedness $model
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('fiscalYear')->latest();

        if (!empty($filters['fiscal_year_id'])) {
            $query->where('fiscal_year_id', (int) $filters['fiscal_year_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('creditor', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $data): StatementOfIndebtedness
    {
        return $this->model->create($data);
    }

    public function update(StatementOfIndebtedness $record, array $data): bool
    {
        return $record->update($data);
    }

    public function delete(StatementOfIndebtedness $record): bool
    {
        return (bool) $record->delete();
    }

    public function getByFiscalYear(int $fiscalYearId): Collection
    {
        return $this->model->with('fiscalYear')
            ->where('fiscal_year_id', $fiscalYearId)
            ->orderBy('creditor')
            ->get();
    }
}
