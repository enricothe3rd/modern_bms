<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObligationRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'obligation_request_id',
        'fund_type_id',
        'department_id',
        'expense_type_id',
        'account_id',
        'sub_account_id',
        'amount',
        'order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function obligationRequest()
    {
        return $this->belongsTo(ObligationRequest::class);
    }

    public function fundType()
    {
        return $this->belongsTo(FundType::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function subAccount()
    {
        return $this->belongsTo(SubAccount::class);
    }
}
