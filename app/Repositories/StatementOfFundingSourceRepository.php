<?php

namespace App\Repositories;

use App\Models\StatementOfFundingSource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StatementOfFundingSourceRepository
{
    public function __construct(
        protected StatementOfFundingSource $model
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['fiscalYear', 'categories.items'])->latest();

        if (!empty($filters['fiscal_year_id'])) {
            $query->where('fiscal_year_id', (int) $filters['fiscal_year_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(array $data): StatementOfFundingSource
    {
        return $this->model->create($data);
    }

    public function update(StatementOfFundingSource $record, array $data): bool
    {
        return $record->update($data);
    }

    public function delete(StatementOfFundingSource $record): bool
    {
        return (bool) $record->delete();
    }

    public function loadFull(StatementOfFundingSource $record): StatementOfFundingSource
    {
        return $record->load(['fiscalYear', 'categories.items']);
    }

    public function getByFiscalYear(int $fiscalYearId): Collection
    {
        return $this->model
            ->with(['fiscalYear', 'categories.items'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->latest()
            ->get();
    }
}
