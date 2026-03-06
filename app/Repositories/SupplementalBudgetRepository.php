<?php

namespace App\Repositories;

use App\Models\SupplementalBudget;
use App\Models\SupplementalBudgetItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplementalBudgetRepository
{
    public function __construct(
        protected SupplementalBudget $model,
        protected SupplementalBudgetItem $itemModel
    ) {}

    public function getAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with(['creator', 'approver', 'items', 'fiscalYear'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['fiscal_year_id'])) {
            $query->where('fiscal_year_id', $filters['fiscal_year_id']);
        }

        if (!empty($filters['group'])) {
            $query->byGroup($filters['group']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?SupplementalBudget
    {
        return $this->model->with([
            'items.department', 
            'items.expenseType', 
            'items.account', 
            'items.subAccount', 
            'creator', 
            'approver',
            'fiscalYear'
        ])->find($id);
    }

    public function create(array $data): SupplementalBudget
    {
        return $this->model->create($data);
    }

    public function update(SupplementalBudget $supplementalBudget, array $data): bool
    {
        return $supplementalBudget->update($data);
    }

    public function delete(SupplementalBudget $supplementalBudget): bool
    {
        return $supplementalBudget->delete();
    }

    public function createItem(SupplementalBudget $supplementalBudget, array $itemData): SupplementalBudgetItem
    {
        return $supplementalBudget->items()->create($itemData);
    }

    public function deleteItems(SupplementalBudget $supplementalBudget): void
    {
        $supplementalBudget->items()->delete();
    }

    public function getAvailableFiscalYears(): SupportCollection
    {
        return $this->model->with('fiscalYear')
            ->get()
            ->pluck('fiscalYear')
            ->filter()
            ->unique('id')
            ->sortBy('year')
            ->values();
    }

    public function getAvailableGroups(): SupportCollection
    {
        return $this->model->distinct()->pluck('supplemental_group')->sort()->values();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->byStatus($status)->get();
    }

    public function getByFiscalYear(int $fiscalYearId): Collection
    {
        return $this->model->where('fiscal_year_id', $fiscalYearId)->get();
    }

    public function getByFiscalYearAndGroup(int $fiscalYearId, string $group): Collection
    {
        return $this->model->where('fiscal_year_id', $fiscalYearId)->byGroup($group)->get();
    }

    public function getTotalAmountByFiscalYear(int $fiscalYearId): float
    {
        return $this->model->where('fiscal_year_id', $fiscalYearId)->where('status', 'approved')->sum('total_amount');
    }

    public function getTotalAmountByGroup(string $group): float
    {
        return $this->model->byGroup($group)->where('status', 'approved')->sum('total_amount');
    }

    public function getItemsByDepartment(int $departmentId): Collection
    {
        return $this->itemModel->with(['supplementalBudget', 'expenseType', 'account', 'subAccount'])
            ->where('department_id', $departmentId)
            ->get();
    }

    public function getItemsByExpenseType(int $expenseTypeId): Collection
    {
        return $this->itemModel->with(['supplementalBudget', 'department', 'account', 'subAccount'])
            ->where('expense_type_id', $expenseTypeId)
            ->get();
    }

    public function getItemsByAccount(int $accountId): Collection
    {
        return $this->itemModel->with(['supplementalBudget', 'department', 'expenseType', 'subAccount'])
            ->where('account_id', $accountId)
            ->get();
    }
}