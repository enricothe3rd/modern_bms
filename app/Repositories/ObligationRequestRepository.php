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
            'items',
            'reviewStatus'
        ])->orderBy('created_at', 'desc')->get();
    }

    public function getByReviewStatuses(array $statusIds)
    {
        return ObligationRequest::with([
            'department',
            'claimantPayee',
            'signatories.user',
            'items',
            'reviewStatus'
        ])
        ->whereIn('review_status_id', $statusIds)
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getByStatusesOrCreatedBy(array $statusIds, $userId)
    {
        return ObligationRequest::with([
            'department',
            'claimantPayee',
            'signatories.user',
            'items',
            'reviewStatus',
            'creator'
        ])
        ->where(function($query) use ($statusIds, $userId) {
            $query->whereIn('review_status_id', $statusIds)
                  ->orWhere('created_by', $userId);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getByCreator($userId)
    {
        return ObligationRequest::with([
            'department',
            'claimantPayee',
            'signatories.user',
            'items',
            'reviewStatus',
            'creator'
        ])
        ->where('created_by', $userId)
        ->orderBy('created_at', 'desc')
        ->get();
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
