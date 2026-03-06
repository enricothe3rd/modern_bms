<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year_id',
        'department_id',
        'group_name',
        'notes',
        'grand_total',
    ];

    protected $casts = [
        'grand_total' => 'decimal:2',
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function items()
    {
        return $this->hasMany(PlantillaItem::class)->orderBy('sort_order');
    }
}
