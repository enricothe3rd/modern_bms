<?php

namespace App\Repositories;

use App\Models\ReviewStatus;

class ReviewStatusRepository
{
    public function all()
    {
        return ReviewStatus::ordered()->get();
    }

    public function active()
    {
        return ReviewStatus::active()->ordered()->get();
    }

    public function find($id)
    {
        return ReviewStatus::findOrFail($id);
    }

    public function create(array $data)
    {
        return ReviewStatus::create($data);
    }

    public function update($id, array $data)
    {
        $reviewStatus = $this->find($id);
        $reviewStatus->update($data);
        return $reviewStatus;
    }

    public function delete($id)
    {
        $reviewStatus = $this->find($id);
        return $reviewStatus->delete();
    }

    public function findByCode($code)
    {
        return ReviewStatus::where('code', $code)->first();
    }
}