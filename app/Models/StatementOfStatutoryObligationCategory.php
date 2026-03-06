<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfStatutoryObligationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_of_statutory_obligation_id',
        'category_number',
        'category_name',
        'sort_order',
    ];

    public function statement()
    {
        return $this->belongsTo(
            StatementOfStatutoryObligation::class,
            'statement_of_statutory_obligation_id'
        );
    }

    public function items()
    {
        return $this->hasMany(StatementOfStatutoryObligationItem::class, 'statement_of_statutory_obligation_category_id')
            ->orderBy('sort_order');
    }
}
