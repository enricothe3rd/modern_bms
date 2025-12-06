<?php

namespace App\Services;

use App\Repositories\SubAccountRepository;
use App\Repositories\AccountRepository;

class SubAccountService
{
    protected $repo;
    protected $accountRepo;

    public function __construct(SubAccountRepository $repo, AccountRepository $accountRepo)
    {
        $this->repo = $repo;
        $this->accountRepo = $accountRepo;
    }

    public function getAllSubAccounts()
    {
        return $this->repo->all();
    }

    public function getAllAccounts()
    {
        return $this->accountRepo->all();
    }

    public function findSubAccount($id)
    {
        return $this->repo->find($id);
    }

    public function createSubAccount(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateSubAccount($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteSubAccount($id)
    {
        return $this->repo->delete($id);
    }
}