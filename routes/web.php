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
use App\Http\Controllers\ReviewStatusController;
use App\Http\Controllers\Api\ObligationRequestApiController;
use App\Http\Controllers\BudgetRealignmentController;
use App\Http\Controllers\FiscalYearController;
use App\Http\Controllers\StatementOfIndebtednessController;
use App\Http\Controllers\StatementOfFundingSourceController;
use App\Http\Controllers\StatementOfStatutoryObligationController;
use App\Http\Controllers\SalaryScheduleController;
use App\Http\Controllers\PlantillaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/reports', function () {
    return view('reports');
})->name('reports');

// Obligation Requests
Route::resource('obligation-requests', ObligationRequestController::class)->except(['create'])->middleware('auth');
Route::post('obligation-requests/{id}/update-status', [ObligationRequestController::class, 'updateStatus'])->name('obligation-requests.update-status')->middleware('auth');

// API routes for cascading dropdowns
Route::prefix('api/obligation-requests')->middleware('auth')->group(function () {
    Route::get('/', [ObligationRequestApiController::class, 'getObligationRequests']);
    Route::get('fund-types/{fundType}/departments', [ObligationRequestApiController::class, 'getDepartmentsByFundType']);
    Route::get('fund-types/{fundType}/departments/{department}/expense-types', [ObligationRequestApiController::class, 'getExpenseTypesByDepartment']);
    Route::get('fund-types/{fundType}/departments/{department}/expense-types/{expenseType}/accounts', [ObligationRequestApiController::class, 'getAccountsByExpenseType']);
    Route::get('fund-types/{fundType}/departments/{department}/expense-types/{expenseType}/accounts/{account}/sub-accounts', [ObligationRequestApiController::class, 'getSubAccountsByAccount']);
});

// Fallback API route for getting expense types by department (without fund type requirement)
Route::get('api/departments/{department}/expense-types', [ObligationRequestApiController::class, 'getExpenseTypesByDepartmentOnly'])->middleware('auth');

// Budget Realignments
Route::resource('budget-realignments', BudgetRealignmentController::class)->middleware('auth');
Route::post('budget-realignments/{budgetRealignment}/submit', [BudgetRealignmentController::class, 'submit'])->name('budget-realignments.submit')->middleware('auth');
Route::post('budget-realignments/{budgetRealignment}/approve', [BudgetRealignmentController::class, 'approve'])->name('budget-realignments.approve')->middleware('auth');
Route::get('api/budget-realignments/allocations', [BudgetRealignmentController::class, 'getAllocations'])->name('budget-realignments.allocations')->middleware('auth');
Route::get('api/budget-realignments/allocation-info', [BudgetRealignmentController::class, 'getAllocationInfo'])->name('budget-realignments.allocation-info')->middleware('auth');

// Fiscal Years
Route::resource('fiscal-years', FiscalYearController::class)->middleware('auth');
Route::post('fiscal-years/{fiscalYear}/set-current', [FiscalYearController::class, 'setCurrent'])->name('fiscal-years.set-current')->middleware('auth');
Route::post('fiscal-years/generate', [FiscalYearController::class, 'generate'])->name('fiscal-years.generate')->middleware('auth');

// Statements of Indebtedness
Route::resource('statements-of-indebtedness', StatementOfIndebtednessController::class)
    ->parameters(['statements-of-indebtedness' => 'statementOfIndebtedness'])
    ->middleware('auth');
Route::resource('statements-of-funding-sources', StatementOfFundingSourceController::class)
    ->parameters(['statements-of-funding-sources' => 'statementOfFundingSource'])
    ->middleware('auth');
Route::get('statements-of-funding-sources/print/fiscal-year', [StatementOfFundingSourceController::class, 'printByFiscalYear'])
    ->name('statements-of-funding-sources.print-fiscal-year')
    ->middleware('auth');
Route::get('statements-of-funding-sources/{statementOfFundingSource}/print', [StatementOfFundingSourceController::class, 'printView'])
    ->name('statements-of-funding-sources.print')
    ->middleware('auth');
Route::resource('statements-of-statutory-obligations', StatementOfStatutoryObligationController::class)
    ->parameters(['statements-of-statutory-obligations' => 'statementOfStatutoryObligation'])
    ->middleware('auth');
Route::get('statements-of-statutory-obligations/print/fiscal-year', [StatementOfStatutoryObligationController::class, 'printByFiscalYear'])
    ->name('statements-of-statutory-obligations.print-fiscal-year')
    ->middleware('auth');
Route::get('statements-of-statutory-obligations/{statementOfStatutoryObligation}/print', [StatementOfStatutoryObligationController::class, 'printView'])
    ->name('statements-of-statutory-obligations.print')
    ->middleware('auth');
Route::resource('salary-schedules', SalaryScheduleController::class)
    ->parameters(['salary-schedules' => 'salarySchedule'])
    ->middleware('auth');
Route::get('salary-schedules/print/fiscal-year', [SalaryScheduleController::class, 'printByFiscalYear'])
    ->name('salary-schedules.print-fiscal-year')
    ->middleware('auth');
Route::get('salary-schedules/{salarySchedule}/print', [SalaryScheduleController::class, 'printView'])
    ->name('salary-schedules.print')
    ->middleware('auth');
Route::resource('plantillas', PlantillaController::class)->middleware('auth');
Route::get('plantillas/print/fiscal-year', [PlantillaController::class, 'printByFiscalYear'])
    ->name('plantillas.print-fiscal-year')
    ->middleware('auth');
Route::get('plantillas/{plantilla}/print', [PlantillaController::class, 'printView'])
    ->name('plantillas.print')
    ->middleware('auth');
Route::get('statements-of-indebtedness/print/fiscal-year', [StatementOfIndebtednessController::class, 'printByFiscalYear'])
    ->name('statements-of-indebtedness.print-fiscal-year')
    ->middleware('auth');
Route::get('statements-of-indebtedness/{statementOfIndebtedness}/print', [StatementOfIndebtednessController::class, 'printView'])
    ->name('statements-of-indebtedness.print')
    ->middleware('auth');

// Public API routes for testing (remove in production)
Route::prefix('api/public')->group(function () {
    Route::get('departments', [DepartmentController::class, 'index']);
    Route::get('accounts', [AccountController::class, 'index']);
});

// PDF Generation Routes
Route::prefix('pdf')->middleware('auth')->group(function () {
    Route::get('templates', [App\Http\Controllers\PdfController::class, 'templates']);
    Route::get('preview/{template}', [App\Http\Controllers\PdfController::class, 'preview']);
    Route::post('generate/{template}', [App\Http\Controllers\PdfController::class, 'generate']);
    Route::post('custom', [App\Http\Controllers\PdfController::class, 'custom']);
});

// Review Status
Route::resource('review-statuses', ReviewStatusController::class)->middleware('auth');

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
// Supplemental Budget Routes
Route::resource('supplemental-budgets', App\Http\Controllers\SupplementalBudgetController::class)->middleware('auth');
Route::post('supplemental-budgets/{supplementalBudget}/submit', [App\Http\Controllers\SupplementalBudgetController::class, 'submit'])->name('supplemental-budgets.submit')->middleware('auth');
Route::post('supplemental-budgets/{supplementalBudget}/approve', [App\Http\Controllers\SupplementalBudgetController::class, 'approve'])->name('supplemental-budgets.approve')->middleware('auth');
Route::post('supplemental-budgets/{supplementalBudget}/reject', [App\Http\Controllers\SupplementalBudgetController::class, 'reject'])->name('supplemental-budgets.reject')->middleware('auth');
Route::get('api/sub-accounts', [App\Http\Controllers\SupplementalBudgetController::class, 'getSubAccounts'])->name('api.sub-accounts')->middleware('auth');
