<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfStatutoryObligationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_of_statutory_obligation_category_id',
        'code',
        'description',
        'amount',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(
            StatementOfStatutoryObligationCategory::class,
            'statement_of_statutory_obligation_category_id'
        );
    }
}
