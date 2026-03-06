<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatementOfStatutoryObligation extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year_id',
        'title',
        'remarks',
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function categories()
    {
        return $this->hasMany(StatementOfStatutoryObligationCategory::class)
            ->orderBy('sort_order');
    }
}
