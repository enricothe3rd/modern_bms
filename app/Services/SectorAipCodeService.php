<?php

namespace App\Services;

use App\Repositories\SectorAipCodeRepository;
use App\Repositories\SectorRepository;

class SectorAipCodeService
{
    protected $repo;
    protected $sectorRepo;

    public function __construct(SectorAipCodeRepository $repo, SectorRepository $sectorRepo)
    {
        $this->repo = $repo;
        $this->sectorRepo = $sectorRepo;
    }

    public function getSector($sectorId)
    {
        return $this->sectorRepo->find($sectorId);
    }

    public function getAipCodesBySector($sectorId)
    {
        return $this->repo->findBySector($sectorId);
    }

    public function createAipCode(array $data, $sectorId)
    {
        $data['sector_id'] = $sectorId;
        $data['is_active'] = $data['is_active'] ?? false;
        
        return $this->repo->create($data);
    }

    public function updateAipCode($id, array $data, $sectorId)
    {
        $data['is_active'] = $data['is_active'] ?? false;
        
        return $this->repo->update($id, $data, $sectorId);
    }

    public function toggleAipCodeStatus($id, $sectorId)
    {
        return $this->repo->toggleStatus($id, $sectorId);
    }

    public function deleteAipCode($id, $sectorId)
    {
        return $this->repo->delete($id, $sectorId);
    }
}
