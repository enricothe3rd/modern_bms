<?php

namespace App\Services;

use App\Repositories\ClaimantPayeeRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\PayeeCategoryRepository;

class ClaimantPayeeService
{
    protected $repo;
    protected $departmentRepo;
    protected $payeeCategoryRepo;

    public function __construct(
        ClaimantPayeeRepository $repo,
        DepartmentRepository $departmentRepo,
        PayeeCategoryRepository $payeeCategoryRepo
    ) {
        $this->repo = $repo;
        $this->departmentRepo = $departmentRepo;
        $this->payeeCategoryRepo = $payeeCategoryRepo;
    }

    public function getAllClaimantPayees()
    {
        return $this->repo->all();
    }

    public function getAllDepartments()
    {
        return $this->departmentRepo->all();
    }

    public function getAllPayeeCategories()
    {
        return $this->payeeCategoryRepo->all();
    }

    public function findClaimantPayee($id)
    {
        return $this->repo->find($id);
    }

    public function createClaimantPayee(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateClaimantPayee($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteClaimantPayee($id)
    {
        return $this->repo->delete($id);
    }
}
