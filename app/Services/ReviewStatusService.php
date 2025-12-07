<?php

namespace App\Services;

use App\Repositories\ReviewStatusRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewStatusService
{
    protected $repo;

    public function __construct(ReviewStatusRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllStatuses()
    {
        return $this->repo->all();
    }

    public function getActiveStatuses()
    {
        return $this->repo->active();
    }

    public function findStatus($id)
    {
        return $this->repo->find($id);
    }

    public function createStatus(array $data)
    {
        // Auto-generate code from name if not provided
        if (empty($data['code'])) {
            $data['code'] = Str::slug($data['name'], '_');
        }

        return $this->repo->create($data);
    }

    public function updateStatus($id, array $data)
    {
        // Auto-generate code from name if not provided
        if (empty($data['code']) && !empty($data['name'])) {
            $data['code'] = Str::slug($data['name'], '_');
        }

        return $this->repo->update($id, $data);
    }

    public function deleteStatus($id)
    {
        return $this->repo->delete($id);
    }
}