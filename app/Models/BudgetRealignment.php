<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetRealignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'realignment_number',
        'description',
        'total_amount',
        'fiscal_year_id',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'remarks'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

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

    public function items(): HasMany
    {
        return $this->hasMany(BudgetRealignmentItem::class);
    }

    public function fromItems(): HasMany
    {
        return $this->hasMany(BudgetRealignmentItem::class)->where('type', 'from');
    }

    public function toItems(): HasMany
    {
        return $this->hasMany(BudgetRealignmentItem::class)->where('type', 'to');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => '#6B7280',
            'pending' => '#F59E0B',
            'approved' => '#10B981',
            'rejected' => '#EF4444',
            default => '#6B7280'
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

    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canApprove(): bool
    {
        return $this->status === 'pending';
    }

    public function canSubmit(): bool
    {
        return $this->status === 'draft';
    }
}
