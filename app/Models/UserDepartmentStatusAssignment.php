<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDepartmentStatusAssignment extends Model
{
    protected $fillable = [
        'user_department_assignment_id',
        'review_status_id',
        'can_approve',
        'can_reject',
        'is_active',
    ];

    protected $casts = [
        'can_approve' => 'boolean',
        'can_reject' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user department assignment
     */
    public function userDepartmentAssignment()
    {
        return $this->belongsTo(UserDepartmentAssignment::class);
    }

    /**
     * Get the review status
     */
    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class);
    }

    /**
     * Scope for active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
