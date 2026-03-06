<?php

namespace App\Repositories;

use App\Models\DepartmentExpenseTypeAllocation;

class DepartmentExpenseTypeAllocationRepository
{
    public function getAllByDepartmentAndExpenseType($departmentId, $expenseTypeId, $fiscalYearId = null)
    {
        $query = DepartmentExpenseTypeAllocation::with(['account', 'subAccount', 'fiscalYear'])
            ->where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId);
            
        if ($fiscalYearId) {
            $query->where('fiscal_year_id', $fiscalYearId);
        }
        
        return $query->orderByDesc('fiscal_year_id')->get();
    }

    public function find($id)
    {
        return DepartmentExpenseTypeAllocation::findOrFail($id);
    }

    public function findByDepartmentExpenseTypeAndId($departmentId, $expenseTypeId, $allocationId)
    {
        return DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->findOrFail($allocationId);
    }

    public function checkDuplicateAllocation($departmentId, $expenseTypeId, $fiscalYearId, $accountId, $subAccountId, $excludeId = null)
    {
        $query = DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->where('account_id', $accountId)
            ->where('sub_account_id', $subAccountId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    public function create(array $data)
    {
        return DepartmentExpenseTypeAllocation::create($data);
    }

    public function update($id, array $data)
    {
        $allocation = $this->find($id);
        $allocation->update($data);
        return $allocation;
    }

    public function delete($id)
    {
        $allocation = $this->find($id);
        return $allocation->delete();
    }
}
