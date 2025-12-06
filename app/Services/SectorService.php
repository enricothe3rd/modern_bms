<?php

namespace App\Services;

use App\Repositories\SectorRepository;

class SectorService
{
    protected $repo;

    public function __construct(SectorRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllSectors()
    {
        return $this->repo->all();
    }
}
