<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'plantilla_id',
        'employee_name',
        'position',
        'old_count',
        'new_count',
        'salary_schedule_id',
        'salary_schedule_from_id',
        'salary_schedule_to_id',
        'salary_from_grade',
        'salary_from_step',
        'salary_to_grade',
        'salary_to_step',
        'salary_from_amount',
        'salary_to_amount',
        'line_total',
        'sort_order',
    ];

    protected $casts = [
        'salary_from_amount' => 'decimal:2',
        'salary_to_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function plantilla()
    {
        return $this->belongsTo(Plantilla::class);
    }

    public function salarySchedule()
    {
        return $this->belongsTo(SalarySchedule::class);
    }

    public function salaryScheduleFrom()
    {
        return $this->belongsTo(SalarySchedule::class, 'salary_schedule_from_id');
    }

    public function salaryScheduleTo()
    {
        return $this->belongsTo(SalarySchedule::class, 'salary_schedule_to_id');
    }

    public function movements()
    {
        return $this->hasMany(PlantillaItemMovement::class)->orderBy('sort_order');
    }

    public function groups()
    {
        return $this->hasMany(PlantillaItemGroup::class)->orderBy('sort_order');
    }

    public function toGroups()
    {
        return $this->groups()->where('group_type', 'to');
    }

    public function fromGroups()
    {
        return $this->groups()->where('group_type', 'from');
    }
}
