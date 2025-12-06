<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'permission_name',
        'permission_description'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Available permissions grouped by modules
    public static function getAvailablePermissions()
    {
        return [
            // PPA & Budget Permissions
            'delete_expense_types' => 'Delete Expense Types from Departments',
            'unreleased_ppa' => 'Unreleased PPA (Change Released PPA back to Saved)',
            'delete_budget_allocations' => 'Delete Budget Allocations',
            'manage_all_departments' => 'Manage All Departments (Override Department Restrictions)',
            
            // Management Module Permissions
            'manage_departments' => 'Manage Departments and Sectors',
            'manage_accounts' => 'Manage Chart of Accounts',
            'manage_sub_accounts' => 'Manage Sub-Account Classifications',
            'manage_expense_types' => 'Manage Expense Types and Categories',
            'manage_roles' => 'Manage User Roles',
            'manage_users' => 'Manage System Users',
            'manage_fund_types' => 'Manage Fund Type Classifications',
            'manage_forms' => 'Manage Form Templates',
            'manage_form_signatories' => 'Manage Form Signatories',
            'manage_user_assignments' => 'Manage User Department Assignments',
            'manage_role_permissions' => 'Manage Role-Based Permissions',
            
            // Specific Delete Permissions
            'delete_departments' => 'Delete Departments and Sectors',
            'delete_accounts' => 'Delete Chart of Accounts',
            'delete_sub_accounts' => 'Delete Sub-Account Classifications',
            'delete_roles' => 'Delete User Roles',
            'delete_users' => 'Delete System Users',
            'delete_fund_types' => 'Delete Fund Type Classifications',
            'delete_forms' => 'Delete Form Templates',
            'delete_form_signatories' => 'Delete Form Signatories',
            'delete_user_assignments' => 'Delete User Department Assignments',
            
            // Reporting & Data Permissions
            'view_financial_reports' => 'View Financial Reports and Analytics',
            'export_data' => 'Export Data (Excel, PDF, CSV)',
        ];
    }

    // Grouped permissions by module for better organization
    public static function getGroupedPermissions()
    {
        return [
            'Department Management' => [
                'icon' => 'building-office',
                'color' => 'blue',
                'permissions' => [
                    'manage_departments' => 'Manage Departments and Sectors',
                    'delete_departments' => 'Delete Departments and Sectors',
                    'manage_all_departments' => 'Manage All Departments (Override Department Restrictions)',
                ]
            ],
            'Financial Management' => [
                'icon' => 'banknotes',
                'color' => 'green',
                'permissions' => [
                    'manage_accounts' => 'Manage Chart of Accounts',
                    'delete_accounts' => 'Delete Chart of Accounts',
                    'manage_sub_accounts' => 'Manage Sub-Account Classifications',
                    'delete_sub_accounts' => 'Delete Sub-Account Classifications',
                    'manage_fund_types' => 'Manage Fund Type Classifications',
                    'delete_fund_types' => 'Delete Fund Type Classifications',
                ]
            ],
            'Budget & PPA Management' => [
                'icon' => 'calculator',
                'color' => 'purple',
                'permissions' => [
                    'manage_expense_types' => 'Manage Expense Types and Categories',
                    'delete_expense_types' => 'Delete Expense Types from Departments',
                    'delete_budget_allocations' => 'Delete Budget Allocations',
                    'unreleased_ppa' => 'Unreleased PPA (Change Released PPA back to Saved)',
                ]
            ],
            'User & Role Management' => [
                'icon' => 'users',
                'color' => 'indigo',
                'permissions' => [
                    'manage_roles' => 'Manage User Roles',
                    'delete_roles' => 'Delete User Roles',
                    'manage_users' => 'Manage System Users',
                    'delete_users' => 'Delete System Users',
                    'manage_user_assignments' => 'Manage User Department Assignments',
                    'delete_user_assignments' => 'Delete User Department Assignments',
                    'manage_role_permissions' => 'Manage Role-Based Permissions',
                ]
            ],
            'Forms & Documents' => [
                'icon' => 'document-text',
                'color' => 'orange',
                'permissions' => [
                    'manage_forms' => 'Manage Form Templates',
                    'delete_forms' => 'Delete Form Templates',
                    'manage_form_signatories' => 'Manage Form Signatories',
                    'delete_form_signatories' => 'Delete Form Signatories',
                ]
            ],
            'Reports & Analytics' => [
                'icon' => 'chart-bar',
                'color' => 'teal',
                'permissions' => [
                    'view_financial_reports' => 'View Financial Reports and Analytics',
                    'export_data' => 'Export Data (Excel, PDF, CSV)',
                ]
            ],
        ];
    }
}