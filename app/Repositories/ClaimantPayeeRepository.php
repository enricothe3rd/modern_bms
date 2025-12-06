<?php

namespace App\Repositories;

use App\Models\ClaimantPayee;

class ClaimantPayeeRepository
{
    public function all()
    {
        return ClaimantPayee::with(['department', 'payeeCategory'])->orderBy('name')->get();
    }

    public function find($id)
    {
        return ClaimantPayee::with(['department', 'payeeCategory'])->findOrFail($id);
    }

    public function create(array $data)
    {
        $claimantPayee = ClaimantPayee::create($data);
        return $claimantPayee->load(['department', 'payeeCategory']);
    }

    public function update($id, array $data)
    {
        $claimantPayee = ClaimantPayee::findOrFail($id);
        $claimantPayee->update($data);
        return $claimantPayee->load(['department', 'payeeCategory']);
    }

    public function delete($id)
    {
        $claimantPayee = ClaimantPayee::findOrFail($id);
        return $claimantPayee->delete();
    }
}
