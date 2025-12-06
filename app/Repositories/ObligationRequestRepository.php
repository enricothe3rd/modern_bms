<?php

namespace App\Repositories;

use App\Models\ObligationRequest;

class ObligationRequestRepository
{
    public function all()
    {
        return ObligationRequest::with([
            'department',
            'claimantPayee',
            'signatories.user',
            'items'
        ])->orderBy('created_at', 'desc')->get();
    }

    public function find($id)
    {
        return ObligationRequest::with([
            'department',
            'claimantPayee',
            'signatories.user',
            'items.fundType',
            'items.department',
            'items.expenseType',
            'items.account',
            'items.subAccount'
        ])->findOrFail($id);
    }

    public function create(array $data)
    {
        return ObligationRequest::create($data);
    }

    public function update($id, array $data)
    {
        $obr = $this->find($id);
        $obr->update($data);
        return $obr;
    }

    public function delete($id)
    {
        $obr = $this->find($id);
        return $obr->delete();
    }
}
