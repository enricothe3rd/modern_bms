<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository
{
    public function all()
    {
        return Account::withCount('subAccounts')->with('subAccounts')->latest()->get();
    }

    public function find($id)
    {
        return Account::findOrFail($id);
    }

    public function create(array $data)
    {
        return Account::create($data);
    }

    public function update($id, array $data)
    {
        $account = $this->find($id);
        $account->update($data);
        return $account;
    }

    public function delete($id)
    {
        $account = $this->find($id);
        return $account->delete();
    }
}