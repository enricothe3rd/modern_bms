<?php

namespace App\Services;

use App\Models\SupplementalBudget;
use App\Repositories\SupplementalBudgetRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplementalBudgetService
{
    public function __construct(
        protected SupplementalBudgetRepository $repository
    ) {}

    public function getAllSupplementalBudgets(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getSupplementalBudgetById(int $id): ?SupplementalBudget
    {
        return $this->repository->findById($id);
    }

    public function createSupplementalBudget(array $data): SupplementalBudget
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'];
            unset($data['items']);

            $data['created_by'] = Auth::id();
            $supplementalBudget = $this->repository->create($data);

            foreach ($items as $itemData) {
                $this->repository->createItem($supplementalBudget, $itemData);
            }

            $supplementalBudget->calculateTotalAmount();

            return $supplementalBudget;
        });
    }

    public function updateSupplementalBudget(SupplementalBudget $supplementalBudget, array $data): SupplementalBudget
    {
        if ($supplementalBudget->status !== 'draft') {
            throw new \Exception('Only draft budgets can be updated.');
        }

        return DB::transaction(function () use ($supplementalBudget, $data) {
            $items = $data['items'];
            unset($data['items']);

            $this->repository->update($supplementalBudget, $data);

            // Delete existing items and create new ones
            $this->repository->deleteItems($supplementalBudget);

            foreach ($items as $itemData) {
                $this->repository->createItem($supplementalBudget, $itemData);
            }

            $supplementalBudget->calculateTotalAmount();

            return $supplementalBudget->fresh();
        });
    }

    public function deleteSupplementalBudget(SupplementalBudget $supplementalBudget): bool
    {
        if ($supplementalBudget->status !== 'draft') {
            throw new \Exception('Only draft budgets can be deleted.');
        }

        return $this->repository->delete($supplementalBudget);
    }

    public function submitForApproval(SupplementalBudget $supplementalBudget): SupplementalBudget
    {
        if ($supplementalBudget->status !== 'draft') {
            throw new \Exception('Only draft budgets can be submitted.');
        }

        $this->repository->update($supplementalBudget, [
            'status' => 'pending',
            'submission_date' => now(),
        ]);

        return $supplementalBudget->fresh();
    }

    public function approve(SupplementalBudget $supplementalBudget, ?string $remarks = null): SupplementalBudget
    {
        if ($supplementalBudget->status !== 'pending') {
            throw new \Exception('Only pending budgets can be approved.');
        }

        $this->repository->update($supplementalBudget, [
            'status' => 'approved',
            'approval_date' => now(),
            'approved_by' => Auth::id(),
            'remarks' => $remarks,
        ]);

        return $supplementalBudget->fresh();
    }

    public function reject(SupplementalBudget $supplementalBudget, string $remarks): SupplementalBudget
    {
        if ($supplementalBudget->status !== 'pending') {
            throw new \Exception('Only pending budgets can be rejected.');
        }

        $this->repository->update($supplementalBudget, [
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'remarks' => $remarks,
        ]);

        return $supplementalBudget->fresh();
    }

    public function getAvailableFiscalYears(): SupportCollection
    {
        return $this->repository->getAvailableFiscalYears();
    }

    public function getAvailableGroups(): SupportCollection
    {
        return $this->repository->getAvailableGroups();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->repository->getByStatus($status);
    }

    public function getByFiscalYear(int $fiscalYearId): Collection
    {
        return $this->repository->getByFiscalYear($fiscalYearId);
    }

    public function getByFiscalYearAndGroup(int $fiscalYearId, string $group): Collection
    {
        return $this->repository->getByFiscalYearAndGroup($fiscalYearId, $group);
    }

    public function getTotalAmountByFiscalYear(int $fiscalYearId): float
    {
        return $this->repository->getTotalAmountByFiscalYear($fiscalYearId);
    }

    public function getTotalAmountByGroup(string $group): float
    {
        return $this->repository->getTotalAmountByGroup($group);
    }

    public function getItemsByDepartment(int $departmentId): Collection
    {
        return $this->repository->getItemsByDepartment($departmentId);
    }

    public function getItemsByExpenseType(int $expenseTypeId): Collection
    {
        return $this->repository->getItemsByExpenseType($expenseTypeId);
    }

    public function getItemsByAccount(int $accountId): Collection
    {
        return $this->repository->getItemsByAccount($accountId);
    }

    public function canEdit(SupplementalBudget $supplementalBudget): bool
    {
        return $supplementalBudget->status === 'draft';
    }

    public function canDelete(SupplementalBudget $supplementalBudget): bool
    {
        return $supplementalBudget->status === 'draft';
    }

    public function canSubmit(SupplementalBudget $supplementalBudget): bool
    {
        return $supplementalBudget->status === 'draft';
    }

    public function canApprove(SupplementalBudget $supplementalBudget): bool
    {
        return $supplementalBudget->status === 'pending';
    }

    public function canReject(SupplementalBudget $supplementalBudget): bool
    {
        return $supplementalBudget->status === 'pending';
    }
}