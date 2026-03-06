<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'acronym'];

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function departmentExpenseTypeAllocations()
    {
        return $this->hasMany(DepartmentExpenseTypeAllocation::class);
    }
}