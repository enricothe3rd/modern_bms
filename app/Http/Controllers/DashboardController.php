<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get system overview data
        $systemStats = [
            'total_items' => \App\Models\Department::count() + 
                           \App\Models\Account::count() + 
                           \App\Models\SubAccount::count() + 
                           \App\Models\ExpenseType::count() + 
                           \App\Models\Role::count() + 
                           \App\Models\User::count() + 
                           \App\Models\FundType::count() + 
                           \App\Models\Form::count() + 
                           \App\Models\FormSignatory::count() + 
                           \App\Models\UserDepartmentAssignment::count(),
            'departments' => \App\Models\Department::count(),
            'users' => \App\Models\User::count(),
            'roles' => \App\Models\Role::count(),
            'forms' => \App\Models\Form::count(),
            'accounts' => \App\Models\Account::count(),
        ];

        return view('dashboard', compact('systemStats'));
    }
}