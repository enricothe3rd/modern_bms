<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManagementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Define all available modules with their permission requirements
        $allModules = [
            [
                'label' => 'Sectors',
                'description' => 'Manage organizational sectors and divisions',
                'icon' => 'squares-2x2',
                'route' => 'sectors.index',
                'color' => 'slate',
                'count' => \App\Models\Sector::count(),
                'permission' => 'manage_departments'
            ],
            [
                'label' => 'Departments',
                'description' => 'Manage organizational departments and their sectors',
                'icon' => 'building-office',
                'route' => 'departments.index',
                'color' => 'blue',
                'count' => \App\Models\Department::count(),
                'permission' => 'manage_departments'
            ],
            [
                'label' => 'Accounts',
                'description' => 'Manage chart of accounts and financial codes',
                'icon' => 'banknotes',
                'route' => 'accounts.index',
                'color' => 'green',
                'count' => \App\Models\Account::count(),
                'permission' => 'manage_accounts'
            ],
            [
                'label' => 'Sub Accounts',
                'description' => 'Manage sub-account classifications',
                'icon' => 'document-text',
                'route' => 'sub-accounts.index',
                'color' => 'emerald',
                'count' => \App\Models\SubAccount::count(),
                'permission' => 'manage_sub_accounts'
            ],
            [
                'label' => 'Expense Types',
                'description' => 'Define expense categories and acronyms',
                'icon' => 'receipt-percent',
                'route' => 'expense-types.index',
                'color' => 'orange',
                'count' => \App\Models\ExpenseType::count(),
                'permission' => 'manage_expense_types'
            ],
            [
                'label' => 'Roles',
                'description' => 'Manage user roles and permissions',
                'icon' => 'shield-check',
                'route' => 'roles.index',
                'color' => 'purple',
                'count' => \App\Models\Role::count(),
                'permission' => 'manage_roles'
            ],
            [
                'label' => 'Users',
                'description' => 'Manage system users and their assignments',
                'icon' => 'users',
                'route' => 'users.index',
                'color' => 'indigo',
                'count' => \App\Models\User::count(),
                'permission' => 'manage_users'
            ],
            [
                'label' => 'Fund Types',
                'description' => 'Manage funding source classifications',
                'icon' => 'currency-dollar',
                'route' => 'fund-types.index',
                'color' => 'teal',
                'count' => \App\Models\FundType::count(),
                'permission' => 'manage_fund_types'
            ],
            [
                'label' => 'Forms',
                'description' => 'Manage form templates and definitions',
                'icon' => 'document-duplicate',
                'route' => 'forms.index',
                'color' => 'cyan',
                'count' => \App\Models\Form::count(),
                'permission' => 'manage_forms'
            ],
            [
                'label' => 'Form Signatories',
                'description' => 'Assign signatories to forms by department',
                'icon' => 'pencil-square',
                'route' => 'form-signatories.index',
                'color' => 'pink',
                'count' => \App\Models\FormSignatory::count(),
                'permission' => 'manage_form_signatories'
            ],
            [
                'label' => 'User Department Assignments',
                'description' => 'Assign users to multiple departments',
                'icon' => 'user-group',
                'route' => 'user-department-assignments.index',
                'color' => 'rose',
                'count' => \App\Models\UserDepartmentAssignment::count(),
                'permission' => 'manage_user_assignments'
            ],
            [
                'label' => 'Role Permissions',
                'description' => 'Manage role-based permissions and access control',
                'icon' => 'key',
                'route' => 'role-permissions.index',
                'color' => 'amber',
                'count' => \App\Models\RolePermission::count(),
                'permission' => 'manage_role_permissions'
            ]
        ];

        // Filter modules based on user permissions
        $managementModules = collect($allModules)->filter(function ($module) use ($user) {
            // If user doesn't have a role, show no modules
            if (!$user || !$user->role) {
                return false;
            }
            
            // Super admin has access to all modules
            if ($user->isSuperAdmin()) {
                return true;
            }
            
            // Check if user has the required permission for this module
            return $user->hasPermission($module['permission']);
        })->values()->toArray();

        // Count of accessible modules for display
        $totalModules = count($allModules);
        $accessibleModules = count($managementModules);

        return view('management.index', compact('managementModules', 'totalModules', 'accessibleModules'));
    }
}