<?php

namespace App\Repositories;

use App\Models\PayeeCategory;

class PayeeCategoryRepository
{
    public function all()
    {
        return PayeeCategory::withCount('claimantPayees')->orderBy('name')->get();
    }

    public function find($id)
    {
        return PayeeCategory::findOrFail($id);
    }

    public function create(array $data)
    {
        return PayeeCategory::create($data);
    }

    public function update($id, array $data)
    {
        $payeeCategory = $this->find($id);
        $payeeCategory->update($data);
        return $payeeCategory;
    }

    public function delete($id)
    {
        $payeeCategory = $this->find($id);
        return $payeeCategory->delete();
    }

    public function hasClaimantPayees($id)
    {
        $payeeCategory = $this->find($id);
        return $payeeCategory->claimantPayees()->count() > 0;
    }
}
