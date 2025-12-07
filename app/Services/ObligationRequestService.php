<?php

namespace App\Services;

use App\Repositories\ObligationRequestRepository;
use App\Models\ObligationRequestSignatory;
use App\Models\ObligationRequestItem;
use Illuminate\Support\Facades\DB;

class ObligationRequestService
{
    protected $repo;

    public function __construct(ObligationRequestRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllObligationRequests()
    {
        return $this->repo->all();
    }

    public function getObligationRequestsByStatuses(array $statusIds, $userId = null)
    {
        return $this->repo->getByStatusesOrCreatedBy($statusIds, $userId);
    }

    public function getObligationRequestsByCreator($userId)
    {
        return $this->repo->getByCreator($userId);
    }

    public function getAllObligationRequestsOld($filterByUserStatuses = false)
    {
        if ($filterByUserStatuses) {
            $user = auth()->user();
            
            // Get user's assigned review status IDs
            $userStatusIds = [];
            if ($user && method_exists($user, 'departmentAssignments')) {
                $userStatusIds = $user->departmentAssignments()
                    ->with('reviewStatuses')
                    ->get()
                    ->pluck('reviewStatuses')
                    ->flatten()
                    ->pluck('id')
                    ->unique()
                    ->toArray();
            }
            
            // If user has assigned statuses, filter by them
            if (!empty($userStatusIds)) {
                return $this->repo->getByReviewStatuses($userStatusIds);
            }
            
            // If no statuses assigned, return empty collection
            return collect([]);
        }
        
        return $this->repo->all();
    }

    public function findObligationRequest($id)
    {
        return $this->repo->find($id);
    }

    public function createObligationRequest(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Get default review status (Draft)
            $defaultStatus = \App\Models\ReviewStatus::where('code', 'draft')->first();
            
            // Create main OBR
            $obr = $this->repo->create([
                'obr_number' => $data['obr_number'],
                'department_id' => $data['department_id'],
                'claimant_payee_id' => $data['claimant_payee_id'],
                'obligation_date' => $data['obligation_date'],
                'particulars' => $data['particulars'],
                'optional_field_1' => $data['optional_field_1'] ?? null,
                'optional_field_2' => $data['optional_field_2'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'review_status_id' => $defaultStatus ? $defaultStatus->id : null,
                'created_by' => auth()->id(),
            ]);

            // Create signatories
            if (isset($data['signatory_1_id'])) {
                ObligationRequestSignatory::create([
                    'obligation_request_id' => $obr->id,
                    'user_id' => $data['signatory_1_id'],
                    'signatory_type' => 'signatory_1',
                    'signatory_date' => $data['signatory_1_date'] ?? null,
                    'order' => 1,
                ]);
            }

            if (isset($data['signatory_2_id'])) {
                ObligationRequestSignatory::create([
                    'obligation_request_id' => $obr->id,
                    'user_id' => $data['signatory_2_id'],
                    'signatory_type' => 'signatory_2',
                    'signatory_date' => $data['signatory_2_date'] ?? null,
                    'order' => 2,
                ]);
            }

            if (isset($data['noted_signatory_id'])) {
                ObligationRequestSignatory::create([
                    'obligation_request_id' => $obr->id,
                    'user_id' => $data['noted_signatory_id'],
                    'signatory_type' => 'noted',
                    'signatory_date' => $data['noted_signatory_date'] ?? null,
                    'order' => 3,
                ]);
            }

            // Create items
            if (isset($data['items']) && is_array($data['items'])) {
                $totalAmount = 0;
                $fundTypeId = $data['fund_type_id'] ?? null; // Get fund type from form level
                
                foreach ($data['items'] as $index => $item) {
                    $accountId = $item['account_id'] ?? null;
                    $subAccountId = $item['sub_account_id'] ?? null;
                    
                    // If sub-account is selected, get the parent account_id
                    if ($subAccountId && !$accountId) {
                        $subAccount = \App\Models\SubAccount::find($subAccountId);
                        if ($subAccount) {
                            $accountId = $subAccount->account_id;
                        }
                    }
                    
                    ObligationRequestItem::create([
                        'obligation_request_id' => $obr->id,
                        'fund_type_id' => $fundTypeId,
                        'department_id' => $item['department_id'],
                        'expense_type_id' => $item['expense_type_id'],
                        'account_id' => $accountId,
                        'sub_account_id' => $subAccountId,
                        'amount' => $item['amount'],
                        'order' => $index + 1,
                    ]);
                    $totalAmount += $item['amount'];
                }

                // Update total amount
                $obr->update(['total_amount' => $totalAmount]);
            }

            $obrWithRelations = $obr->fresh(['signatories', 'items', 'department', 'claimantPayee']);
            
            // Broadcast event for real-time updates
            event(new \App\Events\ObligationRequestCreated($obrWithRelations));

            return $obrWithRelations;
        });
    }

    public function updateObligationRequest($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $obr = $this->repo->update($id, [
                'obr_number' => $data['obr_number'],
                'department_id' => $data['department_id'],
                'claimant_payee_id' => $data['claimant_payee_id'],
                'obligation_date' => $data['obligation_date'],
                'particulars' => $data['particulars'],
                'optional_field_1' => $data['optional_field_1'] ?? null,
                'optional_field_2' => $data['optional_field_2'] ?? null,
                'status' => $data['status'] ?? 'draft',
            ]);

            // Delete existing signatories and recreate
            $obr->signatories()->delete();

            if (isset($data['signatory_1_id'])) {
                ObligationRequestSignatory::create([
                    'obligation_request_id' => $obr->id,
                    'user_id' => $data['signatory_1_id'],
                    'signatory_type' => 'signatory_1',
                    'signatory_date' => $data['signatory_1_date'] ?? null,
                    'order' => 1,
                ]);
            }

            if (isset($data['signatory_2_id'])) {
                ObligationRequestSignatory::create([
                    'obligation_request_id' => $obr->id,
                    'user_id' => $data['signatory_2_id'],
                    'signatory_type' => 'signatory_2',
                    'signatory_date' => $data['signatory_2_date'] ?? null,
                    'order' => 2,
                ]);
            }

            if (isset($data['noted_signatory_id'])) {
                ObligationRequestSignatory::create([
                    'obligation_request_id' => $obr->id,
                    'user_id' => $data['noted_signatory_id'],
                    'signatory_type' => 'noted',
                    'signatory_date' => $data['noted_signatory_date'] ?? null,
                    'order' => 3,
                ]);
            }

            // Delete existing items and recreate
            $obr->items()->delete();

            if (isset($data['items']) && is_array($data['items'])) {
                $totalAmount = 0;
                $fundTypeId = $data['fund_type_id'] ?? null; // Get fund type from form level
                
                foreach ($data['items'] as $index => $item) {
                    $accountId = $item['account_id'] ?? null;
                    $subAccountId = $item['sub_account_id'] ?? null;
                    
                    // If sub-account is selected, get the parent account_id
                    if ($subAccountId && !$accountId) {
                        $subAccount = \App\Models\SubAccount::find($subAccountId);
                        if ($subAccount) {
                            $accountId = $subAccount->account_id;
                        }
                    }
                    
                    ObligationRequestItem::create([
                        'obligation_request_id' => $obr->id,
                        'fund_type_id' => $fundTypeId,
                        'department_id' => $item['department_id'],
                        'expense_type_id' => $item['expense_type_id'],
                        'account_id' => $accountId,
                        'sub_account_id' => $subAccountId,
                        'amount' => $item['amount'],
                        'order' => $index + 1,
                    ]);
                    $totalAmount += $item['amount'];
                }

                $obr->update(['total_amount' => $totalAmount]);
            }

            $obrWithRelations = $obr->fresh(['signatories', 'items', 'department', 'claimantPayee']);
            
            // Broadcast event for real-time updates
            event(new \App\Events\ObligationRequestUpdated($obrWithRelations));

            return $obrWithRelations;
        });
    }

    public function deleteObligationRequest($id)
    {
        $obr = $this->repo->find($id);
        $obrNumber = $obr->obr_number;
        
        $result = $this->repo->delete($id);
        
        // Broadcast event for real-time updates
        event(new \App\Events\ObligationRequestDeleted($id, $obrNumber));
        
        return $result;
    }

    public function updateReviewStatus($id, $reviewStatusId)
    {
        $obr = $this->repo->update($id, [
            'review_status_id' => $reviewStatusId
        ]);

        // Broadcast event for real-time updates
        event(new \App\Events\ObligationRequestUpdated($obr));

        return $obr;
    }
}
