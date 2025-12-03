<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
use App\Validators\DepartmentValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentService
{
    protected $repo;

    public function __construct(DepartmentRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllDepartments()
    {
        return $this->repo->all();
    }

    public function findDepartment($id)
    {
        return $this->repo->find($id);
    }

    public function createDepartment(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateDepartment($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteDepartment($id)
    {
        return $this->repo->delete($id);
    }
}
