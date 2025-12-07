<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDepartmentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'is_active',
        'start_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    /**
     * Get the user that owns the assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department for this assignment.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope to get only active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get assignments for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get assignments for a specific department.
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Get the status assignments for this user-department combination
     */
    public function statusAssignments()
    {
        return $this->hasMany(UserDepartmentStatusAssignment::class);
    }

    /**
     * Get the review statuses assigned to this user-department combination
     */
    public function reviewStatuses()
    {
        return $this->belongsToMany(ReviewStatus::class, 'user_department_status_assignments')
            ->withPivot('can_approve', 'can_reject', 'is_active')
            ->withTimestamps();
    }
}
