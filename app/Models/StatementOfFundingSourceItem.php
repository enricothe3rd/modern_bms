<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfFundingSourceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_of_funding_source_category_id',
        'particulars',
        'account_classification',
        'amount',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(StatementOfFundingSourceCategory::class, 'statement_of_funding_source_category_id');
    }
}
