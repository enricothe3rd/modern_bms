<?php

namespace App\Repositories;

use App\Models\BudgetRealignment;
use App\Models\BudgetRealignmentItem;
use App\Models\DepartmentExpenseTypeAllocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BudgetRealignmentRepository
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return BudgetRealignment::with(['creator', 'approver', 'fiscalYear'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByFiscalYear(int $fiscalYearId, int $perPage = 15): LengthAwarePaginator
    {
        return BudgetRealignment::with(['creator', 'approver', 'fiscalYear'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?BudgetRealignment
    {
        return BudgetRealignment::with([
            'creator',
            'approver',
            'fiscalYear',
            'fromItems.allocation.department',
            'fromItems.allocation.expenseType',
            'fromItems.allocation.account',
            'fromItems.allocation.subAccount',
            'fromItems.allocation.fiscalYear',
            'toItems.allocation.department',
            'toItems.allocation.expenseType',
            'toItems.allocation.account',
            'toItems.allocation.subAccount',
            'toItems.allocation.fiscalYear'
        ])->find($id);
    }

    public function create(array $data): BudgetRealignment
    {
        return BudgetRealignment::create($data);
    }

    public function update(BudgetRealignment $realignment, array $data): bool
    {
        return $realignment->update($data);
    }

    public function delete(BudgetRealignment $realignment): bool
    {
        return $realignment->delete();
    }

    public function createItem(array $data): BudgetRealignmentItem
    {
        return BudgetRealignmentItem::create($data);
    }

    public function deleteItems(BudgetRealignment $realignment): bool
    {
        return $realignment->items()->delete();
    }

    public function getLastRealignmentNumber(int $fiscalYearId): int
    {
        return BudgetRealignment::where('fiscal_year_id', $fiscalYearId)
            ->where('realignment_number', 'like', "BR-%-")
            ->count();
    }

    public function getAllocationsByFiscalYear(
        int $fiscalYearId,
        ?int $fundTypeId = null,
        ?int $departmentId = null,
        ?int $expenseTypeId = null
    ): Collection {
        $query = DepartmentExpenseTypeAllocation::with([
            'department',
            'expenseType',
            'account',
            'subAccount',
            'fiscalYear'
        ])->where('fiscal_year_id', $fiscalYearId);

        if ($fundTypeId) {
            $query->whereHas('department', function ($q) use ($fundTypeId) {
                $q->where('fund_type_id', $fundTypeId);
            });
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($expenseTypeId) {
            $query->where('expense_type_id', $expenseTypeId);
        }

        return $query->get();
    }

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return BudgetRealignment::with(['creator', 'approver', 'fiscalYear'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByCreator(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return BudgetRealignment::with(['creator', 'approver', 'fiscalYear'])
            ->where('created_by', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getPendingApprovals(int $perPage = 15): LengthAwarePaginator
    {
        return BudgetRealignment::with(['creator', 'approver', 'fiscalYear'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }
}