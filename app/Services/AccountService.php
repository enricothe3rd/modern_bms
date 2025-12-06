<?php

namespace App\Services;

use App\Repositories\AccountRepository;

class AccountService
{
    protected $repo;

    public function __construct(AccountRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllAccounts()
    {
        return $this->repo->all();
    }

    public function findAccount($id)
    {
        return $this->repo->find($id);
    }

    public function createAccount(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateAccount($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteAccount($id)
    {
        return $this->repo->delete($id);
    }
}