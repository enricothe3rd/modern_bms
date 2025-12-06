<?php

namespace App\Repositories;

use App\Models\FundType;

class FundTypeRepository
{
    public function all()
    {
        return FundType::orderBy('description')->get();
    }

    public function find($id)
    {
        return FundType::findOrFail($id);
    }
}
