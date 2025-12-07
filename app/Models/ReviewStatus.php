<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewStatus extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get user department assignments that can handle this status
     */
    public function userDepartmentStatusAssignments()
    {
        return $this->hasMany(UserDepartmentStatusAssignment::class);
    }

    /**
     * Scope for active statuses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered statuses
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
