<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SubAccountController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FundTypeController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormSignatoryController;
use App\Http\Controllers\UserDepartmentAssignmentController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObligationRequestController;
use App\Http\Controllers\Api\ObligationRequestApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/reports', function () {
    return view('reports');
})->name('reports');

// Obligation Requests
Route::resource('obligation-requests', ObligationRequestController::class)->except(['create'])->middleware('auth');

// API routes for cascading dropdowns
Route::prefix('api/obligation-requests')->middleware('auth')->group(function () {
    Route::get('fund-types/{fundType}/departments', [ObligationRequestApiController::class, 'getDepartmentsByFundType']);
    Route::get('fund-types/{fundType}/departments/{department}/expense-types', [ObligationRequestApiController::class, 'getExpenseTypesByDepartment']);
    Route::get('fund-types/{fundType}/departments/{department}/expense-types/{expenseType}/accounts', [ObligationRequestApiController::class, 'getAccountsByExpenseType']);
    Route::get('fund-types/{fundType}/departments/{department}/expense-types/{expenseType}/accounts/{account}/sub-accounts', [ObligationRequestApiController::class, 'getSubAccountsByAccount']);
});

Route::get('/setUp', function () {
    return view('setUp.index');
})->name('setUp');


// sectors
Route::resource('sectors', App\Http\Controllers\SectorController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_departments');
Route::delete('sectors/{sector}', [App\Http\Controllers\SectorController::class, 'destroy'])->name('sectors.destroy')->middleware('permission:delete_departments');

// Sector AIP Codes Management (Separate Module)
Route::get('sector-aip-codes/{sector}', [App\Http\Controllers\SectorAipCodeController::class, 'index'])->name('sector-aip-codes.index')->middleware('permission:manage_departments');
Route::post('sector-aip-codes/{sector}', [App\Http\Controllers\SectorAipCodeController::class, 'store'])->name('sector-aip-codes.store')->middleware('permission:manage_departments');
Route::put('sector-aip-codes/{sector}/{aipCode}', [App\Http\Controllers\SectorAipCodeController::class, 'update'])->name('sector-aip-codes.update')->middleware('permission:manage_departments');
Route::put('sector-aip-codes/{sector}/{aipCode}/toggle', [App\Http\Controllers\SectorAipCodeController::class, 'toggle'])->name('sector-aip-codes.toggle')->middleware('permission:manage_departments');
Route::delete('sector-aip-codes/{sector}/{aipCode}', [App\Http\Controllers\SectorAipCodeController::class, 'destroy'])->name('sector-aip-codes.destroy')->middleware('permission:delete_departments');

// departments
Route::resource('departments', DepartmentController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_departments');
Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy')->middleware('permission:delete_departments');

// accounts
Route::resource('accounts', AccountController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_accounts');
Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy')->middleware('permission:delete_accounts');

// sub-accounts
Route::resource('sub-accounts', SubAccountController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_sub_accounts');
Route::delete('sub-accounts/{sub_account}', [SubAccountController::class, 'destroy'])->name('sub-accounts.destroy')->middleware('permission:delete_sub_accounts');

// expense-types
Route::resource('expense-types', ExpenseTypeController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_expense_types');
Route::delete('expense-types/{expense_type}', [ExpenseTypeController::class, 'destroy'])->name('expense-types.destroy')->middleware('permission:manage_expense_types');

// roles
Route::resource('roles', RoleController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_roles');
Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete_roles');

// users
Route::resource('users', UserController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_users');
Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete_users');
Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password')->middleware('permission:manage_users');

// fund-types
Route::resource('fund-types', FundTypeController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_fund_types');
Route::delete('fund-types/{fund_type}', [FundTypeController::class, 'destroy'])->name('fund-types.destroy')->middleware('permission:delete_fund_types');

// forms
Route::resource('forms', FormController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_forms');
Route::delete('forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy')->middleware('permission:delete_forms');

// form-signatories
Route::resource('form-signatories', FormSignatoryController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_form_signatories');
Route::delete('form-signatories/{form_signatory}', [FormSignatoryController::class, 'destroy'])->name('form-signatories.destroy')->middleware('permission:delete_form_signatories');

// user-department-assignments
Route::resource('user-department-assignments', UserDepartmentAssignmentController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_user_assignments');
Route::delete('user-department-assignments/{user_department_assignment}', [UserDepartmentAssignmentController::class, 'destroy'])->name('user-department-assignments.destroy')->middleware('permission:delete_user_assignments');

// payee-categories
Route::resource('payee-categories', App\Http\Controllers\PayeeCategoryController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_payees');
Route::delete('payee-categories/{payee_category}', [App\Http\Controllers\PayeeCategoryController::class, 'destroy'])->name('payee-categories.destroy')->middleware('permission:delete_payees');

// claimant-payees
Route::resource('claimant-payees', App\Http\Controllers\ClaimantPayeeController::class)->except(['create', 'show', 'destroy'])->middleware('permission:manage_payees');
Route::delete('claimant-payees/{claimant_payee}', [App\Http\Controllers\ClaimantPayeeController::class, 'destroy'])->name('claimant-payees.destroy')->middleware('permission:delete_payees');

// management dashboard
Route::get('/management', [ManagementController::class, 'index'])->name('management.index');

// role permissions
Route::get('/role-permissions', [App\Http\Controllers\RolePermissionController::class, 'index'])->name('role-permissions.index')->middleware('permission:manage_role_permissions');
Route::post('/role-permissions', [App\Http\Controllers\RolePermissionController::class, 'store'])->name('role-permissions.store')->middleware('permission:manage_role_permissions');
Route::get('/role-permissions/{role}', [App\Http\Controllers\RolePermissionController::class, 'show'])->name('role-permissions.show')->middleware('permission:manage_role_permissions');

// department expense types
Route::get('/departments/{department}/expense-types', [App\Http\Controllers\DepartmentExpenseTypeController::class, 'index'])->name('department-expense-types.index');
Route::post('/departments/{department}/expense-types', [App\Http\Controllers\DepartmentExpenseTypeController::class, 'store'])->name('department-expense-types.store');
Route::delete('/departments/{department}/expense-types/{expenseType}', [App\Http\Controllers\DepartmentExpenseTypeController::class, 'destroy'])->name('department-expense-types.destroy')->middleware('permission:delete_expense_types');
Route::put('/departments/{department}/expense-types/{expenseType}/ppa-info', [App\Http\Controllers\DepartmentExpenseTypeController::class, 'updatePpaInfo'])->name('department-expense-types.update-ppa-info');
Route::put('/departments/{department}/expense-types/{expenseType}/release', [App\Http\Controllers\DepartmentExpenseTypeController::class, 'releasePpa'])->name('department-expense-types.release-ppa');
Route::put('/departments/{department}/expense-types/{expenseType}/unreleased', [App\Http\Controllers\DepartmentExpenseTypeController::class, 'unreleasePpa'])->name('department-expense-types.unreleased-ppa')->middleware('permission:unreleased_ppa');

// department expense type allocations
Route::get('/departments/{department}/expense-types/{expenseType}/allocations', [App\Http\Controllers\DepartmentExpenseTypeAllocationController::class, 'index'])->name('department-expense-type-allocations.index');
Route::post('/departments/{department}/expense-types/{expenseType}/allocations', [App\Http\Controllers\DepartmentExpenseTypeAllocationController::class, 'store'])->name('department-expense-type-allocations.store');
Route::get('/departments/{department}/expense-types/{expenseType}/allocations/{allocation}/edit', [App\Http\Controllers\DepartmentExpenseTypeAllocationController::class, 'edit'])->name('department-expense-type-allocations.edit');
Route::put('/departments/{department}/expense-types/{expenseType}/allocations/{allocation}', [App\Http\Controllers\DepartmentExpenseTypeAllocationController::class, 'update'])->name('department-expense-type-allocations.update');
Route::delete('/departments/{department}/expense-types/{expenseType}/allocations/{allocation}', [App\Http\Controllers\DepartmentExpenseTypeAllocationController::class, 'destroy'])->name('department-expense-type-allocations.destroy')->middleware('permission:delete_budget_allocations');
Route::get('/accounts/{account}/sub-accounts', [App\Http\Controllers\DepartmentExpenseTypeAllocationController::class, 'getAccountSubAccounts'])->name('accounts.sub-accounts');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
