<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplementalBudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplemental_budget_id',
        'department_id',
        'expense_type_id',
        'account_id',
        'sub_account_id',
        'amount',
        'justification',
        'remarks'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function supplementalBudget(): BelongsTo
    {
        return $this->belongsTo(SupplementalBudget::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function subAccount(): BelongsTo
    {
        return $this->belongsTo(SubAccount::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            $item->supplementalBudget->calculateTotalAmount();
        });

        static::deleted(function ($item) {
            $item->supplementalBudget->calculateTotalAmount();
        });
    }
}