<?php

namespace App\Repositories;

use App\Models\UserDepartmentAssignment;

class UserDepartmentAssignmentRepository
{
    public function allGroupedByUser()
    {
        return UserDepartmentAssignment::with(['user.role', 'department'])
            ->orderBy('user_id')
            ->orderBy('department_id')
            ->get()
            ->groupBy(function($item) {
                return $item->user ? $item->user->name : 'Unknown User';
            });
    }

    public function find($id)
    {
        return UserDepartmentAssignment::findOrFail($id);
    }

    public function create(array $data)
    {
        $assignment = UserDepartmentAssignment::create($data);
        return $assignment->load(['user.role', 'department']);
    }

    public function update($id, array $data)
    {
        $assignment = $this->find($id);
        $assignment->update($data);
        return $assignment->load(['user.role', 'department']);
    }

    public function delete($id)
    {
        $assignment = $this->find($id);
        return $assignment->delete();
    }

    public function existsForUserAndDepartment($userId, $departmentId, $excludeId = null)
    {
        $query = UserDepartmentAssignment::where('user_id', $userId)
            ->where('department_id', $departmentId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public function findByUserAndDepartment($userId, $departmentId)
    {
        return UserDepartmentAssignment::where('user_id', $userId)
            ->where('department_id', $departmentId)
            ->first();
    }
}
