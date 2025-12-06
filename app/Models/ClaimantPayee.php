<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimantPayee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'department_id',
        'payee_category_id',
    ];

    /**
     * Get the department that owns the claimant payee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the category that owns the claimant payee.
     */
    public function payeeCategory()
    {
        return $this->belongsTo(PayeeCategory::class);
    }
}
