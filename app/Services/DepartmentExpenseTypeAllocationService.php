<?php

namespace App\Services;

use App\Repositories\DepartmentExpenseTypeAllocationRepository;
use App\Models\Account;
use App\Models\SubAccount;

class DepartmentExpenseTypeAllocationService
{
    protected $repo;

    public function __construct(DepartmentExpenseTypeAllocationRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllocations($departmentId, $expenseTypeId)
    {
        return $this->repo->getAllByDepartmentAndExpenseType($departmentId, $expenseTypeId);
    }

    public function getAllocation($departmentId, $expenseTypeId, $allocationId)
    {
        return $this->repo->findByDepartmentExpenseTypeAndId($departmentId, $expenseTypeId, $allocationId);
    }

    public function createAllocation($departmentId, $expenseTypeId, array $data)
    {
        // Validate account can be allocated to
        if ($data['account_type'] === 'account' && $data['account_id']) {
            $account = Account::findOrFail($data['account_id']);
            if (!$account->canBeAllocatedTo()) {
                throw new \Exception('Cannot allocate to this account because it has sub-accounts. Please select one of its sub-accounts instead.');
            }
        }

        // Validate sub-account has valid parent
        if ($data['account_type'] === 'sub_account' && $data['sub_account_id']) {
            $subAccount = SubAccount::with('account')->findOrFail($data['sub_account_id']);
            if (!$subAccount->account) {
                throw new \Exception('Selected sub-account does not have a valid parent account.');
            }
        }

        // Check for duplicate allocation
        $accountId = $data['account_type'] === 'account' ? $data['account_id'] : null;
        $subAccountId = $data['account_type'] === 'sub_account' ? $data['sub_account_id'] : null;

        $existingAllocation = $this->repo->checkDuplicateAllocation(
            $departmentId,
            $expenseTypeId,
            $data['year'],
            $accountId,
            $subAccountId
        );

        if ($existingAllocation) {
            throw new \Exception('An allocation for this account and year already exists. Please edit the existing allocation instead.');
        }

        // Create allocation
        return $this->repo->create([
            'department_id' => $departmentId,
            'expense_type_id' => $expenseTypeId,
            'year' => $data['year'],
            'account_id' => $accountId,
            'sub_account_id' => $subAccountId,
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null
        ]);
    }

    public function updateAllocation($departmentId, $expenseTypeId, $allocationId, array $data)
    {
        // Validate account can be allocated to
        if ($data['account_type'] === 'account' && $data['account_id']) {
            $account = Account::findOrFail($data['account_id']);
            if (!$account->canBeAllocatedTo()) {
                throw new \Exception('Cannot allocate to this account because it has sub-accounts. Please select one of its sub-accounts instead.');
            }
        }

        // Check for duplicate allocation (excluding current)
        $accountId = $data['account_type'] === 'account' ? $data['account_id'] : null;
        $subAccountId = $data['account_type'] === 'sub_account' ? $data['sub_account_id'] : null;

        $existingAllocation = $this->repo->checkDuplicateAllocation(
            $departmentId,
            $expenseTypeId,
            $data['year'],
            $accountId,
            $subAccountId,
            $allocationId
        );

        if ($existingAllocation) {
            throw new \Exception('An allocation for this account and year already exists.');
        }

        // Update allocation
        return $this->repo->update($allocationId, [
            'year' => $data['year'],
            'account_id' => $accountId,
            'sub_account_id' => $subAccountId,
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null
        ]);
    }

    public function deleteAllocation($allocationId)
    {
        return $this->repo->delete($allocationId);
    }
}
