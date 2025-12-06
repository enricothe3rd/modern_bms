<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Relationship to users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relationship to permissions
    public function permissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    // Check if role has specific permission
    public function hasPermission($permissionName)
    {
        return $this->permissions()->where('permission_name', $permissionName)->exists();
    }

    // Get all permission names for this role
    public function getPermissionNames()
    {
        return $this->permissions()->pluck('permission_name')->toArray();
    }
}