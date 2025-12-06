<?php

namespace App\Repositories;

use App\Models\SubAccount;

class SubAccountRepository
{
    public function all()
    {
        return SubAccount::with('account')->latest()->get();
    }

    public function find($id)
    {
        return SubAccount::findOrFail($id);
    }

    public function create(array $data)
    {
        return SubAccount::create($data);
    }

    public function update($id, array $data)
    {
        $subAccount = $this->find($id);
        $subAccount->update($data);
        return $subAccount;
    }

    public function delete($id)
    {
        $subAccount = $this->find($id);
        return $subAccount->delete();
    }
}