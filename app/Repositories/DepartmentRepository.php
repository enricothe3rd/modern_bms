<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository
{
    public function all()
    {
        return Department::latest()->get();
    }

    public function find($id)
    {
        return Department::findOrFail($id);
    }

    public function create(array $data)
    {
        return Department::create($data);
    }

    public function update($id, array $data)
    {
        $department = $this->find($id);
        $department->update($data);
        return $department;
    }

    public function delete($id)
    {
        $department = $this->find($id);
        return $department->delete();
    }
}
