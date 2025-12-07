<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
        'avatar',
        'phone',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role that the user belongs to.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the department that the user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all department assignments for this user.
     */
    public function departmentAssignments()
    {
        return $this->hasMany(UserDepartmentAssignment::class);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->role && strtolower($this->role->name) === 'super admin';
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission($permissionName)
    {
        if (!$this->role) {
            return false;
        }
        
        // Super admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        return $this->role->hasPermission($permissionName);
    }

    /**
     * Check if user can delete expense types
     */
    public function canDeleteExpenseTypes()
    {
        return $this->hasPermission('delete_expense_types');
    }

    /**
     * Check if user can unreleased PPA
     */
    public function canUnreleasedPpa()
    {
        return $this->hasPermission('unreleased_ppa');
    }

    /**
     * Check if user can delete budget allocations
     */
    public function canDeleteBudgetAllocations()
    {
        return $this->hasPermission('delete_budget_allocations');
    }

    /**
     * Check if user can manage all departments
     */
    public function canManageAllDepartments()
    {
        return $this->hasPermission('manage_all_departments');
    }

    // Management Module Permissions
    public function canManageDepartments()
    {
        return $this->hasPermission('manage_departments');
    }

    public function canManageAccounts()
    {
        return $this->hasPermission('manage_accounts');
    }

    public function canManageSubAccounts()
    {
        return $this->hasPermission('manage_sub_accounts');
    }

    public function canManageExpenseTypes()
    {
        return $this->hasPermission('manage_expense_types');
    }

    public function canManageRoles()
    {
        return $this->hasPermission('manage_roles');
    }

    public function canManageUsers()
    {
        return $this->hasPermission('manage_users');
    }

    public function canManageFundTypes()
    {
        return $this->hasPermission('manage_fund_types');
    }

    public function canManageForms()
    {
        return $this->hasPermission('manage_forms');
    }

    public function canManageFormSignatories()
    {
        return $this->hasPermission('manage_form_signatories');
    }

    public function canManageUserAssignments()
    {
        return $this->hasPermission('manage_user_assignments');
    }

    public function canManageRolePermissions()
    {
        return $this->hasPermission('manage_role_permissions');
    }

    // Specific Delete Permissions
    public function canDeleteDepartments()
    {
        return $this->hasPermission('delete_departments');
    }

    public function canDeleteAccounts()
    {
        return $this->hasPermission('delete_accounts');
    }

    public function canDeleteSubAccounts()
    {
        return $this->hasPermission('delete_sub_accounts');
    }

    public function canDeleteRoles()
    {
        return $this->hasPermission('delete_roles');
    }

    public function canDeleteUsers()
    {
        return $this->hasPermission('delete_users');
    }

    public function canDeleteFundTypes()
    {
        return $this->hasPermission('delete_fund_types');
    }

    public function canDeleteForms()
    {
        return $this->hasPermission('delete_forms');
    }

    public function canDeleteFormSignatories()
    {
        return $this->hasPermission('delete_form_signatories');
    }

    public function canDeleteUserAssignments()
    {
        return $this->hasPermission('delete_user_assignments');
    }

    /**
     * Get the user's avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }
        
        return null;
    }

    /**
     * Get user initials for avatar placeholder
     */
    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';
        
        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }
}
