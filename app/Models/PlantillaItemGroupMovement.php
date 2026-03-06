<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaItemGroupMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'plantilla_item_group_id',
        'label',
        'salary_schedule_id',
        'salary_grade',
        'salary_step',
        'salary_amount',
        'movement_total',
        'sort_order',
    ];

    protected $casts = [
        'salary_amount' => 'decimal:2',
        'movement_total' => 'decimal:2',
    ];

    public function group()
    {
        return $this->belongsTo(PlantillaItemGroup::class, 'plantilla_item_group_id');
    }

    public function salarySchedule()
    {
        return $this->belongsTo(SalarySchedule::class, 'salary_schedule_id');
    }
}
