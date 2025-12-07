<?php

namespace App\Services;

use App\Repositories\UserDepartmentAssignmentRepository;
use App\Repositories\UserRepository;
use App\Repositories\DepartmentRepository;

class UserDepartmentAssignmentService
{
    protected $repo;
    protected $userRepo;
    protected $departmentRepo;

    public function __construct(
        UserDepartmentAssignmentRepository $repo,
        UserRepository $userRepo,
        DepartmentRepository $departmentRepo
    ) {
        $this->repo = $repo;
        $this->userRepo = $userRepo;
        $this->departmentRepo = $departmentRepo;
    }

    public function getAllAssignmentsGroupedByUser()
    {
        return $this->repo->allGroupedByUser();
    }

    public function getAllUsers()
    {
        return $this->userRepo->allWithRole();
    }

    public function getAllDepartments()
    {
        return $this->departmentRepo->all();
    }

    public function findAssignment($id)
    {
        return $this->repo->find($id);
    }

    public function createAssignments($userId, array $departmentIds, array $assignmentData, array $reviewStatusIds = [])
    {
        $createdAssignments = [];
        $existingDepartments = [];

        foreach ($departmentIds as $departmentId) {
            // Check if this combination already exists
            if ($this->repo->existsForUserAndDepartment($userId, $departmentId)) {
                $department = $this->departmentRepo->find($departmentId);
                $existingDepartments[] = $department->name;
                continue;
            }

            $assignment = $this->repo->create([
                'user_id' => $userId,
                'department_id' => $departmentId,
                ...$assignmentData
            ]);

            // Sync review statuses for this assignment
            if (!empty($reviewStatusIds)) {
                $assignment->reviewStatuses()->sync($reviewStatusIds);
            }

            $createdAssignments[] = $assignment;
        }

        return [
            'created' => $createdAssignments,
            'existing' => $existingDepartments
        ];
    }

    public function updateAssignment($id, $userId, $departmentId, array $assignmentData, array $reviewStatusIds = [])
    {
        // Check if this combination already exists (excluding current record)
        if ($this->repo->existsForUserAndDepartment($userId, $departmentId, $id)) {
            throw new \Exception('This user is already assigned to this department');
        }

        $assignment = $this->repo->update($id, [
            'user_id' => $userId,
            'department_id' => $departmentId,
            ...$assignmentData
        ]);

        // Sync review statuses for this assignment
        $assignment->reviewStatuses()->sync($reviewStatusIds);

        return $assignment;
    }

    public function deleteAssignment($id)
    {
        return $this->repo->delete($id);
    }
}
