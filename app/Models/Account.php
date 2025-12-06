<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'description'];

    // Relationship to sub-accounts
    public function subAccounts()
    {
        return $this->hasMany(SubAccount::class);
    }

    // Check if this account has sub-accounts
    public function hasSubAccounts()
    {
        return $this->subAccounts()->exists();
    }

    // Check if this account can be allocated to (doesn't have sub-accounts)
    public function canBeAllocatedTo()
    {
        return !$this->hasSubAccounts();
    }

    // Scope to get only accounts that can be allocated to (leaf accounts)
    public function scopeAllocatable($query)
    {
        return $query->doesntHave('subAccounts');
    }
}