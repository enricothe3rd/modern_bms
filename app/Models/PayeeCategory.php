<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayeeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the claimant payees for the category.
     */
    public function claimantPayees()
    {
        return $this->hasMany(ClaimantPayee::class);
    }
}
