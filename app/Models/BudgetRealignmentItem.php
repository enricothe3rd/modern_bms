<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetRealignmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_realignment_id',
        'type',
        'department_expense_type_allocation_id',
        'amount',
        'remarks'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function budgetRealignment(): BelongsTo
    {
        return $this->belongsTo(BudgetRealignment::class);
    }

    public function allocation(): BelongsTo
    {
        return $this->belongsTo(DepartmentExpenseTypeAllocation::class, 'department_expense_type_allocation_id');
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type === 'from' ? '#EF4444' : '#10B981';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'from' ? 'From (Source)' : 'To (Destination)';
    }
}
