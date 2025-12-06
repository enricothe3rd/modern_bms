<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService
{
    protected $repo;

    public function __construct(RoleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllRoles()
    {
        return $this->repo->all();
    }

    public function findRole($id)
    {
        return $this->repo->find($id);
    }

    public function createRole(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateRole($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteRole($id)
    {
        return $this->repo->delete($id);
    }
}