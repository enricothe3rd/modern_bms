<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryScheduleCell extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_schedule_id',
        'salary_grade',
        'step_no',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function salarySchedule()
    {
        return $this->belongsTo(SalarySchedule::class);
    }
}
