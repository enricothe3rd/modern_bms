<?php

namespace App\Repositories;

use App\Models\ClaimantPayee;

class ClaimantPayeeRepository
{
    public function all()
    {
        return ClaimantPayee::with(['department', 'payeeCategory'])
            ->orderBy('name')
            ->get();
    }

    public function find($id)
    {
        return ClaimantPayee::findOrFail($id);
    }

    public function create(array $data)
    {
        return ClaimantPayee::create($data);
    }

    public function update($id, array $data)
    {
        $claimantPayee = $this->find($id);
        $claimantPayee->update($data);
        return $claimantPayee;
    }

    public function delete($id)
    {
        $claimantPayee = $this->find($id);
        return $claimantPayee->delete();
    }
}
