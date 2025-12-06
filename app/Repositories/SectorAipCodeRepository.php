<?php

namespace App\Repositories;

use App\Models\SectorAipCode;

class SectorAipCodeRepository
{
    public function findBySector($sectorId)
    {
        return SectorAipCode::where('sector_id', $sectorId)
            ->orderBy('code')
            ->get();
    }

    public function find($id, $sectorId = null)
    {
        $query = SectorAipCode::query();
        
        if ($sectorId) {
            $query->where('sector_id', $sectorId);
        }
        
        return $query->findOrFail($id);
    }

    public function create(array $data)
    {
        return SectorAipCode::create($data);
    }

    public function update($id, array $data, $sectorId = null)
    {
        $aipCode = $this->find($id, $sectorId);
        $aipCode->update($data);
        return $aipCode;
    }

    public function delete($id, $sectorId = null)
    {
        $aipCode = $this->find($id, $sectorId);
        return $aipCode->delete();
    }

    public function toggleStatus($id, $sectorId = null)
    {
        $aipCode = $this->find($id, $sectorId);
        $aipCode->is_active = !$aipCode->is_active;
        $aipCode->save();
        return $aipCode;
    }
}
