<?php

namespace App\Services;

use App\Repositories\RolePermissionRepository;
use App\Repositories\RoleRepository;

class RolePermissionService
{
    protected $repo;
    protected $roleRepo;

    public function __construct(RolePermissionRepository $repo, RoleRepository $roleRepo)
    {
        $this->repo = $repo;
        $this->roleRepo = $roleRepo;
    }

    public function getAllRolesWithPermissions()
    {
        return $this->roleRepo->allWithPermissions();
    }

    public function getAvailablePermissions()
    {
        return $this->repo->getAvailablePermissions();
    }

    public function getGroupedPermissions()
    {
        return $this->repo->getGroupedPermissions();
    }

    public function getRoleWithPermissions($roleId)
    {
        return $this->roleRepo->findWithPermissions($roleId);
    }

    public function getAssignedPermissions($roleId)
    {
        return $this->repo->getByRole($roleId)->pluck('permission_name')->toArray();
    }

    public function updateRolePermissions($roleId, array $permissions)
    {
        $role = $this->roleRepo->find($roleId);
        $availablePermissions = array_keys($this->repo->getAvailablePermissions());

        // Remove existing permissions for this role
        $this->repo->deleteByRole($roleId);

        // Add new permissions
        foreach ($permissions as $permission) {
            if (in_array($permission, $availablePermissions)) {
                $this->repo->create([
                    'role_id' => $role->id,
                    'permission_name' => $permission,
                    'permission_description' => $this->repo->getAvailablePermissions()[$permission]
                ]);
            }
        }

        return $role;
    }
}
