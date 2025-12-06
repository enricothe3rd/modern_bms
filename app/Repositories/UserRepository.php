<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function allWithRole()
    {
        return User::with(['role', 'department'])->latest()->get();
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        $user = User::create($data);
        return $user->load(['role', 'department']);
    }

    public function update($id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user->load(['role', 'department']);
    }

    public function delete($id)
    {
        $user = $this->find($id);
        return $user->delete();
    }
}
