<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    public function all()
    {
        return Role::withCount('users')->latest()->get();
    }

    public function find($id)
    {
        return Role::findOrFail($id);
    }

    public function create(array $data)
    {
        return Role::create($data);
    }

    public function update($id, array $data)
    {
        $role = $this->find($id);
        $role->update($data);
        return $role;
    }

    public function delete($id)
    {
        $role = $this->find($id);
        return $role->delete();
    }

    public function allWithPermissions()
    {
        return Role::with('permissions')->get();
    }

    public function findWithPermissions($id)
    {
        return Role::with('permissions')->findOrFail($id);
    }
}