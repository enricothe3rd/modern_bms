<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\DepartmentRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $repo;
    protected $roleRepo;
    protected $departmentRepo;

    public function __construct(
        UserRepository $repo,
        RoleRepository $roleRepo,
        DepartmentRepository $departmentRepo
    ) {
        $this->repo = $repo;
        $this->roleRepo = $roleRepo;
        $this->departmentRepo = $departmentRepo;
    }

    public function getAllUsers()
    {
        return $this->repo->allWithRole();
    }

    public function getAllRoles()
    {
        return $this->roleRepo->all();
    }

    public function getAllDepartments()
    {
        return $this->departmentRepo->all();
    }

    public function findUser($id)
    {
        return $this->repo->find($id);
    }

    public function createUser(array $data)
    {
        // Use default password if none provided
        $password = !empty($data['password']) ? $data['password'] : '123456';
        
        $data['password'] = Hash::make($password);
        
        return $this->repo->create($data);
    }

    public function updateUser($id, array $data)
    {
        // Only hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        return $this->repo->update($id, $data);
    }

    public function resetUserPassword($id)
    {
        return $this->repo->update($id, [
            'password' => Hash::make('123456')
        ]);
    }

    public function deleteUser($id)
    {
        return $this->repo->delete($id);
    }
}
