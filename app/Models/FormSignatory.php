<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSignatory extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'form_name', // Keep for backward compatibility during migration
        'department_id',
        'signatory_id',
        'order'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function signatory()
    {
        return $this->belongsTo(User::class, 'signatory_id');
    }

    // Helper method to get form name (supports both old and new structure)
    public function getFormNameAttribute()
    {
        return $this->form ? $this->form->name : $this->attributes['form_name'];
    }
}
