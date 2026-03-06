<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplementalBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'fiscal_year_id',
        'supplemental_group',
        'status',
        'total_amount',
        'submission_date',
        'approval_date',
        'created_by',
        'approved_by',
        'remarks'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'submission_date' => 'date',
        'approval_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SupplementalBudgetItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    public function calculateTotalAmount(): void
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->save();
    }

    public function scopeByFiscalYear($query, $fiscalYearId)
    {
        return $query->where('fiscal_year_id', $fiscalYearId);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('supplemental_group', $group);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}