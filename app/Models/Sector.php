<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the departments for the sector.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the AIP codes for the sector.
     */
    public function aipCodes()
    {
        return $this->hasMany(SectorAipCode::class);
    }

    /**
     * Get only active AIP codes for the sector.
     */
    public function activeAipCodes()
    {
        return $this->hasMany(SectorAipCode::class)->where('is_active', true);
    }
}
