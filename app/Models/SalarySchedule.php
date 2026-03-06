<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year_id',
        'title',
        'effective_date',
        'notes',
        'total_steps',
        'max_grade',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function cells()
    {
        return $this->hasMany(SalaryScheduleCell::class)
            ->orderBy('salary_grade')
            ->orderBy('step_no');
    }

    public function cellsByGrade(): array
    {
        return $this->cells
            ->groupBy('salary_grade')
            ->map(fn ($group) => $group->keyBy('step_no'))
            ->toArray();
    }
}
