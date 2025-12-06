<?php

namespace App\Repositories;

use App\Models\ExpenseType;

class ExpenseTypeRepository
{
    public function all()
    {
        return ExpenseType::latest()->get();
    }

    public function find($id)
    {
        return ExpenseType::findOrFail($id);
    }

    public function create(array $data)
    {
        return ExpenseType::create($data);
    }

    public function update($id, array $data)
    {
        $expenseType = $this->find($id);
        $expenseType->update($data);
        return $expenseType;
    }

    public function delete($id)
    {
        $expenseType = $this->find($id);
        return $expenseType->delete();
    }
}