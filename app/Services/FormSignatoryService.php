<?php

namespace App\Services;

use App\Repositories\FormSignatoryRepository;
use App\Repositories\FormRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;

class FormSignatoryService
{
    protected $repo;
    protected $formRepo;
    protected $departmentRepo;
    protected $userRepo;

    public function __construct(
        FormSignatoryRepository $repo,
        FormRepository $formRepo,
        DepartmentRepository $departmentRepo,
        UserRepository $userRepo
    ) {
        $this->repo = $repo;
        $this->formRepo = $formRepo;
        $this->departmentRepo = $departmentRepo;
        $this->userRepo = $userRepo;
    }

    public function getAllSignatoriesGrouped()
    {
        return $this->repo->allGrouped();
    }

    public function getAllForms()
    {
        return $this->formRepo->allActive();
    }

    public function getAllDepartments()
    {
        return $this->departmentRepo->all();
    }

    public function getAllUsers()
    {
        return $this->userRepo->allWithRole();
    }

    public function findSignatory($id)
    {
        return $this->repo->find($id);
    }

    public function getSignatoryIdsByFormAndDepartment($formId, $departmentId)
    {
        return $this->repo->getSignatoryIdsByFormAndDepartment($formId, $departmentId);
    }

    public function assignSignatories($formId, $departmentId, array $signatoryIds)
    {
        // Delete existing signatories for this form and department
        $this->repo->deleteByFormAndDepartment($formId, $departmentId);

        // Create new signatories with order
        foreach ($signatoryIds as $index => $signatoryId) {
            $this->repo->create([
                'form_id' => $formId,
                'department_id' => $departmentId,
                'signatory_id' => $signatoryId,
                'order' => $index + 1
            ]);
        }

        return $this->repo->getByFormAndDepartment($formId, $departmentId);
    }

    public function assignSignatoriesToAllDepartments($formId, array $signatoryIds)
    {
        // Get all departments
        $departments = $this->departmentRepo->all();

        // Delete existing signatories for this form across all departments
        $this->repo->deleteByForm($formId);

        // Create signatories for each department
        foreach ($departments as $department) {
            foreach ($signatoryIds as $index => $signatoryId) {
                $this->repo->create([
                    'form_id' => $formId,
                    'department_id' => $department->id,
                    'signatory_id' => $signatoryId,
                    'order' => $index + 1
                ]);
            }
        }

        return [
            'signatories' => $this->repo->getByForm($formId),
            'department_count' => $departments->count()
        ];
    }

    public function deleteSignatoriesByFormAndDepartment($formId, $departmentId)
    {
        return $this->repo->deleteByFormAndDepartment($formId, $departmentId);
    }
}
