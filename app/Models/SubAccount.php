<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAccount extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'description', 'account_id'];

    // Relationship to parent account
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}