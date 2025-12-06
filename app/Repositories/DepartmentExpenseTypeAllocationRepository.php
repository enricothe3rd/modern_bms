<?php

namespace App\Repositories;

use App\Models\DepartmentExpenseTypeAllocation;

class DepartmentExpenseTypeAllocationRepository
{
    public function getAllByDepartmentAndExpenseType($departmentId, $expenseTypeId)
    {
        return DepartmentExpenseTypeAllocation::with(['account', 'subAccount'])
            ->where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->orderBy('year', 'desc')
            ->get();
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

    public function checkDuplicateAllocation($departmentId, $expenseTypeId, $year, $accountId, $subAccountId, $excludeId = null)
    {
        $query = DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->where('year', $year)
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
