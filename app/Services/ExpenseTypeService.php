<?php

namespace App\Services;

use App\Repositories\ExpenseTypeRepository;

class ExpenseTypeService
{
    protected $repo;

    public function __construct(ExpenseTypeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllExpenseTypes()
    {
        return $this->repo->all();
    }

    public function findExpenseType($id)
    {
        return $this->repo->find($id);
    }

    public function createExpenseType(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateExpenseType($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteExpenseType($id)
    {
        return $this->repo->delete($id);
    }
}