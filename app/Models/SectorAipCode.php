<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorAipCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'sector_id',
        'code',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the sector that owns the AIP code.
     */
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
