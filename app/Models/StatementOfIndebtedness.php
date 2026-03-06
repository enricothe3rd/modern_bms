<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfIndebtedness extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year_id',
        'creditor',
        'date_contracted',
        'term_maturity',
        'principal_amount',
        'purpose',
        'prev_principal',
        'prev_interest',
        'prev_total',
        'due_principal',
        'due_interest',
        'due_total',
        'balance',
    ];

    protected $casts = [
        'date_contracted' => 'date',
        'principal_amount' => 'decimal:2',
        'prev_principal' => 'decimal:2',
        'prev_interest' => 'decimal:2',
        'prev_total' => 'decimal:2',
        'due_principal' => 'decimal:2',
        'due_interest' => 'decimal:2',
        'due_total' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }
}
