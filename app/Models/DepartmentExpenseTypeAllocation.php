<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentExpenseTypeAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'expense_type_id',
        'fiscal_year_id',
        'account_id',
        'sub_account_id',
        'amount',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function subAccount()
    {
        return $this->belongsTo(SubAccount::class);
    }

    public function getAccountDisplayAttribute()
    {
        if ($this->account) {
            return $this->account->code . ' - ' . $this->account->description;
        } elseif ($this->subAccount) {
            return $this->subAccount->code . ' - ' . $this->subAccount->description;
        }
        return 'No Account';
    }

    public function getAccountTypeAttribute()
    {
        return $this->account ? 'account' : 'sub_account';
    }

    public function getParentAccountInfoAttribute()
    {
        if ($this->subAccount && $this->subAccount->account) {
            return [
                'code' => $this->subAccount->account->code,
                'description' => $this->subAccount->account->description,
                'display' => $this->subAccount->account->code . ' - ' . $this->subAccount->account->description
            ];
        }
        return null;
    }

    public function hasParentAccountConflict($selectedAccountId = null)
    {
        if (!$this->subAccount || !$selectedAccountId) {
            return false;
        }
        
        return $this->subAccount->account_id !== $selectedAccountId;
    }

    public function getObligatedAmount($fiscalYearId = null)
    {
        $fiscalYearId = $fiscalYearId ?? $this->fiscal_year_id;
        
        if (!$fiscalYearId) {
            return 0;
        }
        
        return \App\Models\ObligationRequestItem::whereHas('obligationRequest', function($query) use ($fiscalYearId) {
                $query->whereHas('fiscalYear', function($subQuery) use ($fiscalYearId) {
                    $subQuery->where('id', $fiscalYearId);
                });
            })
            ->where('department_id', $this->department_id)
            ->where('expense_type_id', $this->expense_type_id)
            ->where(function($query) {
                if ($this->account_id) {
                    $query->where('account_id', $this->account_id);
                } elseif ($this->sub_account_id) {
                    $query->where('sub_account_id', $this->sub_account_id);
                }
            })
            ->sum('amount') ?? 0;
    }

    public function getAvailableAmount($fiscalYearId = null)
    {
        return $this->amount - $this->getObligatedAmount($fiscalYearId);
    }
}