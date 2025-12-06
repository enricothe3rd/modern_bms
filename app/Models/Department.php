<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'sector_id'];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function expenseTypes()
    {
        return $this->belongsToMany(ExpenseType::class)
            ->withPivot(['ppa_code', 'date_issued', 'status'])
            ->withTimestamps();
    }
}
