<?php

namespace App\Repositories;

use App\Models\Sector;

class SectorRepository
{
    public function all()
    {
        return Sector::withCount(['departments', 'aipCodes'])
            ->orderBy('name')
            ->get();
    }

    public function find($id)
    {
        return Sector::findOrFail($id);
    }
}
