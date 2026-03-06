<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaItemMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'plantilla_item_id',
        'label',
        'salary_schedule_from_id',
        'salary_schedule_to_id',
        'salary_from_grade',
        'salary_from_step',
        'salary_to_grade',
        'salary_to_step',
        'salary_from_amount',
        'salary_to_amount',
        'movement_total',
        'sort_order',
    ];

    protected $casts = [
        'salary_from_amount' => 'decimal:2',
        'salary_to_amount' => 'decimal:2',
        'movement_total' => 'decimal:2',
    ];

    public function plantillaItem()
    {
        return $this->belongsTo(PlantillaItem::class);
    }

    public function salaryScheduleFrom()
    {
        return $this->belongsTo(SalarySchedule::class, 'salary_schedule_from_id');
    }

    public function salaryScheduleTo()
    {
        return $this->belongsTo(SalarySchedule::class, 'salary_schedule_to_id');
    }
}
