<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FiscalYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'is_current'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
    ];

    /**
     * Get the current fiscal year
     */
    public static function current()
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Get active fiscal years
     */
    public static function active()
    {
        return static::where('is_active', true)->orderBy('year', 'desc');
    }

    /**
     * Set this fiscal year as current (and unset others)
     */
    public function setCurrent()
    {
        // Unset all other current fiscal years
        static::where('is_current', true)->update(['is_current' => false]);
        
        // Set this one as current
        $this->update(['is_current' => true, 'is_active' => true]);
    }

    /**
     * Check if this fiscal year contains a given date
     */
    public function containsDate($date)
    {
        $date = Carbon::parse($date);
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * Get fiscal year by year value
     */
    public static function findByYear($year)
    {
        return static::where('year', $year)->first();
    }

    /**
     * Generate default fiscal year data
     */
    public static function generateForYear($year)
    {
        return [
            'year' => $year,
            'description' => "Fiscal Year {$year}",
            'start_date' => Carbon::create($year, 1, 1),
            'end_date' => Carbon::create($year, 12, 31),
            'is_active' => true,
            'is_current' => $year == date('Y')
        ];
    }
}