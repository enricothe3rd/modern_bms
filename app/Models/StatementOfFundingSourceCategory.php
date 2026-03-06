<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfFundingSourceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_of_funding_source_id',
        'category_number',
        'category_name',
        'amount',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function statement()
    {
        return $this->belongsTo(StatementOfFundingSource::class, 'statement_of_funding_source_id');
    }

    public function items()
    {
        return $this->hasMany(StatementOfFundingSourceItem::class, 'statement_of_funding_source_category_id')
            ->orderBy('sort_order');
    }
}
