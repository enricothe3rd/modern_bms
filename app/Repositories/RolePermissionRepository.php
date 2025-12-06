<?php

namespace App\Repositories;

use App\Models\RolePermission;

class RolePermissionRepository
{
    public function getAvailablePermissions()
    {
        return RolePermission::getAvailablePermissions();
    }

    public function getGroupedPermissions()
    {
        return RolePermission::getGroupedPermissions();
    }

    public function deleteByRole($roleId)
    {
        return RolePermission::where('role_id', $roleId)->delete();
    }

    public function create(array $data)
    {
        return RolePermission::create($data);
    }

    public function getByRole($roleId)
    {
        return RolePermission::where('role_id', $roleId)->get();
    }
}
