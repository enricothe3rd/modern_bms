<?php

namespace App\Services;

use App\Models\BudgetRealignment;
use App\Models\DepartmentExpenseTypeAllocation;
use App\Models\FiscalYear;
use App\Repositories\BudgetRealignmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetRealignmentService
{
    public function __construct(
        private BudgetRealignmentRepository $repository
    ) {}

    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($perPage);
    }

    public function getByFiscalYear(int $fiscalYearId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByFiscalYear($fiscalYearId, $perPage);
    }

    public function findById(int $id): ?BudgetRealignment
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): BudgetRealignment
    {
        return DB::transaction(function () use ($data) {
            // Validate fiscal year
            $fiscalYear = FiscalYear::findOrFail($data['fiscal_year_id']);
            
            // Generate realignment number
            $lastNumber = $this->repository->getLastRealignmentNumber($data['fiscal_year_id']);
            $realignmentNumber = "BR-{$fiscalYear->year}-" . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // Calculate total amount
            $totalFromAmount = collect($data['from_items'])->sum('amount');

            // Create realignment
            $realignment = $this->repository->create([
                'realignment_number' => $realignmentNumber,
                'description' => $data['description'],
                'total_amount' => $totalFromAmount,
                'fiscal_year_id' => $data['fiscal_year_id'],
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Create from items
            foreach ($data['from_items'] as $fromItem) {
                // Find or create the allocation
                $allocation = $this->findOrCreateAllocation(
                    $fromItem['department_id'],
                    $fromItem['expense_type_id'],
                    $fromItem['account_id'],
                    $fromItem['sub_account_id'] ?? null,
                    $data['fiscal_year_id']
                );

                $this->repository->createItem([
                    'budget_realignment_id' => $realignment->id,
                    'type' => 'from',
                    'department_expense_type_allocation_id' => $allocation->id,
                    'amount' => $fromItem['amount'],
                    'remarks' => $fromItem['remarks'] ?? null,
                ]);
            }

            // Create to items
            foreach ($data['to_items'] as $toItem) {
                // Find or create the allocation
                $allocation = $this->findOrCreateAllocation(
                    $toItem['department_id'],
                    $toItem['expense_type_id'],
                    $toItem['account_id'],
                    $toItem['sub_account_id'] ?? null,
                    $data['fiscal_year_id']
                );

                $this->repository->createItem([
                    'budget_realignment_id' => $realignment->id,
                    'type' => 'to',
                    'department_expense_type_allocation_id' => $allocation->id,
                    'amount' => $toItem['amount'],
                    'remarks' => $toItem['remarks'] ?? null,
                ]);
            }

            return $realignment;
        });
    }

    public function update(BudgetRealignment $realignment, array $data): BudgetRealignment
    {
        if (!$realignment->canEdit()) {
            throw new \Exception('This realignment cannot be edited in its current status.');
        }

        return DB::transaction(function () use ($realignment, $data) {
            // Calculate total amount
            $totalFromAmount = collect($data['from_items'])->sum('amount');

            // Update realignment
            $this->repository->update($realignment, [
                'description' => $data['description'],
                'total_amount' => $totalFromAmount,
            ]);

            // Delete existing items
            $this->repository->deleteItems($realignment);

            // Create new from items
            foreach ($data['from_items'] as $fromItem) {
                // Find or create the allocation
                $allocation = $this->findOrCreateAllocation(
                    $fromItem['department_id'],
                    $fromItem['expense_type_id'],
                    $fromItem['account_id'],
                    $fromItem['sub_account_id'] ?? null,
                    $realignment->fiscal_year_id
                );

                $this->repository->createItem([
                    'budget_realignment_id' => $realignment->id,
                    'type' => 'from',
                    'department_expense_type_allocation_id' => $allocation->id,
                    'amount' => $fromItem['amount'],
                    'remarks' => $fromItem['remarks'] ?? null,
                ]);
            }

            // Create new to items
            foreach ($data['to_items'] as $toItem) {
                // Find or create the allocation
                $allocation = $this->findOrCreateAllocation(
                    $toItem['department_id'],
                    $toItem['expense_type_id'],
                    $toItem['account_id'],
                    $toItem['sub_account_id'] ?? null,
                    $realignment->fiscal_year_id
                );

                $this->repository->createItem([
                    'budget_realignment_id' => $realignment->id,
                    'type' => 'to',
                    'department_expense_type_allocation_id' => $allocation->id,
                    'amount' => $toItem['amount'],
                    'remarks' => $toItem['remarks'] ?? null,
                ]);
            }

            return $realignment->fresh();
        });
    }

    public function delete(BudgetRealignment $realignment): bool
    {
        if (!$realignment->canEdit()) {
            throw new \Exception('This realignment cannot be deleted in its current status.');
        }

        return $this->repository->delete($realignment);
    }

    public function submit(BudgetRealignment $realignment): BudgetRealignment
    {
        if (!$realignment->canSubmit()) {
            throw new \Exception('This realignment cannot be submitted in its current status.');
        }

        $this->repository->update($realignment, ['status' => 'pending']);
        
        return $realignment->fresh();
    }

    public function approve(BudgetRealignment $realignment, ?string $remarks = null): BudgetRealignment
    {
        if (!$realignment->canApprove()) {
            throw new \Exception('This realignment cannot be approved in its current status.');
        }

        return DB::transaction(function () use ($realignment, $remarks) {
            // Apply the realignment to actual allocations
            foreach ($realignment->fromItems as $fromItem) {
                $allocation = $fromItem->allocation;
                $allocation->decrement('amount', $fromItem->amount);
            }

            foreach ($realignment->toItems as $toItem) {
                $allocation = $toItem->allocation;
                $allocation->increment('amount', $toItem->amount);
            }

            $this->repository->update($realignment, [
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'remarks' => $remarks
            ]);

            return $realignment->fresh();
        });
    }

    public function reject(BudgetRealignment $realignment, ?string $remarks = null): BudgetRealignment
    {
        if (!$realignment->canApprove()) {
            throw new \Exception('This realignment cannot be rejected in its current status.');
        }

        $this->repository->update($realignment, [
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'remarks' => $remarks
        ]);

        return $realignment->fresh();
    }

    public function getAllocationsByFiscalYear(
        int $fiscalYearId,
        ?int $fundTypeId = null,
        ?int $departmentId = null,
        ?int $expenseTypeId = null
    ): array {
        $allocations = $this->repository->getAllocationsByFiscalYear(
            $fiscalYearId,
            $fundTypeId,
            $departmentId,
            $expenseTypeId
        );

        return $allocations->map(function ($allocation) {
            $obligatedAmount = $allocation->getObligatedAmount();
            $availableAmount = $allocation->amount - $obligatedAmount;

            return [
                'id' => $allocation->id,
                'department_name' => $allocation->department->name,
                'expense_type_description' => $allocation->expenseType->description,
                'account_description' => $allocation->account ? $allocation->account->description : 'N/A',
                'sub_account_description' => $allocation->subAccount ? $allocation->subAccount->description : null,
                'allocated_amount' => $allocation->amount,
                'obligated_amount' => $obligatedAmount,
                'available_amount' => $availableAmount,
                'display_text' => $this->formatAllocationDisplay($allocation, $availableAmount)
            ];
        })->toArray();
    }

    public function validateAmounts(array $fromItems, array $toItems): array
    {
        $errors = [];
        
        // Validate that total from amounts equal total to amounts
        $totalFromAmount = collect($fromItems)->sum('amount');
        $totalToAmount = collect($toItems)->sum('amount');

        if (abs($totalFromAmount - $totalToAmount) > 0.01) {
            $errors[] = 'Total "From" amount must equal total "To" amount.';
        }

        return $errors;
    }

    public function validateAvailableFunds(array $fromItems, int $fiscalYearId): array
    {
        $errors = [];
        
        // Validate that source allocations have sufficient available amounts
        foreach ($fromItems as $fromItem) {
            $allocation = DepartmentExpenseTypeAllocation::find($fromItem['allocation_id']);
            
            if (!$allocation || $allocation->fiscal_year_id !== $fiscalYearId) {
                $errors[] = "Invalid allocation selected.";
                continue;
            }
            
            $availableAmount = $allocation->amount - $allocation->getObligatedAmount();
            
            if ($fromItem['amount'] > $availableAmount) {
                $errors[] = "Insufficient funds in allocation: {$allocation->department->name} - {$allocation->expenseType->description}. Available: ₱" . number_format($availableAmount, 2);
            }
        }

        return $errors;
    }

    private function formatAllocationDisplay($allocation, $availableAmount): string
    {
        $text = "{$allocation->department->name} - {$allocation->expenseType->description}";
        
        if ($allocation->account) {
            $text .= " - {$allocation->account->description}";
        }
        
        if ($allocation->subAccount) {
            $text .= " - {$allocation->subAccount->description}";
        }
        
        $text .= " [Available: ₱" . number_format($availableAmount, 2) . "]";
        
        return $text;
    }

    public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByStatus($status, $perPage);
    }

    public function getByCreator(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getByCreator($userId, $perPage);
    }

    public function getPendingApprovals(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPendingApprovals($perPage);
    }

    private function findOrCreateAllocation(int $departmentId, int $expenseTypeId, int $accountId, ?int $subAccountId, int $fiscalYearId)
    {
        // Try to find existing allocation
        $allocation = \App\Models\DepartmentExpenseTypeAllocation::where([
            'department_id' => $departmentId,
            'expense_type_id' => $expenseTypeId,
            'account_id' => $accountId,
            'sub_account_id' => $subAccountId,
            'fiscal_year_id' => $fiscalYearId,
        ])->first();

        // If not found, create a new one with zero amount
        if (!$allocation) {
            $allocation = \App\Models\DepartmentExpenseTypeAllocation::create([
                'department_id' => $departmentId,
                'expense_type_id' => $expenseTypeId,
                'account_id' => $accountId,
                'sub_account_id' => $subAccountId,
                'fiscal_year_id' => $fiscalYearId,
                'amount' => 0.00,
            ]);
        }

        return $allocation;
    }
}