<?php

namespace App\Services;

use App\Repositories\PayeeCategoryRepository;

class PayeeCategoryService
{
    protected $repo;

    public function __construct(PayeeCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllPayeeCategories()
    {
        return $this->repo->all();
    }

    public function findPayeeCategory($id)
    {
        return $this->repo->find($id);
    }

    public function createPayeeCategory(array $data)
    {
        return $this->repo->create($data);
    }

    public function updatePayeeCategory($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deletePayeeCategory($id)
    {
        // Check if category has claimant payees
        if ($this->repo->hasClaimantPayees($id)) {
            throw new \Exception('Cannot delete category that has claimant payees assigned to it.');
        }

        return $this->repo->delete($id);
    }
}
