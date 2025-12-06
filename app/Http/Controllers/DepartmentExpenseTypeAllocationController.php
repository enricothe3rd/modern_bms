<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExpenseType;
use App\Models\Account;
use App\Models\SubAccount;
use App\Http\Requests\DepartmentExpenseTypeAllocationRequest;
use App\Services\DepartmentExpenseTypeAllocationService;
use Illuminate\Http\Request;

class DepartmentExpenseTypeAllocationController extends Controller
{
    protected $service;

    public function __construct(DepartmentExpenseTypeAllocationService $service)
    {
        $this->service = $service;
    }

    public function index($departmentId, $expenseTypeId)
    {
        $department = Department::with('sector')->findOrFail($departmentId);
        $expenseType = ExpenseType::findOrFail($expenseTypeId);
        
        $allocations = $this->service->getAllocations($departmentId, $expenseTypeId);

        // Load accounts with their sub-accounts for hierarchical display
        $accounts = Account::with('subAccounts')->orderBy('code')->get();
        $subAccounts = SubAccount::with('account')->get();

        return view('department-expense-type-allocations.index', compact(
            'department', 
            'expenseType', 
            'allocations', 
            'accounts', 
            'subAccounts'
        ));
    }

    public function store(DepartmentExpenseTypeAllocationRequest $request, $departmentId, $expenseTypeId)
    {
        try {
            $this->service->createAllocation($departmentId, $expenseTypeId, $request->validated());

            return redirect()->route('department-expense-type-allocations.index', [$departmentId, $expenseTypeId])
                ->with('success', 'Budget allocation added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($departmentId, $expenseTypeId, $allocationId)
    {
        $department = Department::with('sector')->findOrFail($departmentId);
        $expenseType = ExpenseType::findOrFail($expenseTypeId);
        $allocation = DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->findOrFail($allocationId);

        // Show all accounts (including parent accounts)
        $accounts = Account::all();
        $subAccounts = SubAccount::with('account')->get();

        return response()->json([
            'allocation' => $allocation,
            'accounts' => $accounts,
            'subAccounts' => $subAccounts
        ]);
    }

    public function update(DepartmentExpenseTypeAllocationRequest $request, $departmentId, $expenseTypeId, $allocationId)
    {
        try {
            $this->service->updateAllocation($departmentId, $expenseTypeId, $allocationId, $request->validated());

            return redirect()->route('department-expense-type-allocations.index', [$departmentId, $expenseTypeId])
                ->with('success', 'Budget allocation updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($departmentId, $expenseTypeId, $allocationId)
    {
        try {
            $this->service->deleteAllocation($allocationId);

            return redirect()->route('department-expense-type-allocations.index', [$departmentId, $expenseTypeId])
                ->with('success', 'Allocation removed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function getAccountSubAccounts($accountId)
    {
        $account = Account::with('subAccounts')->findOrFail($accountId);
        
        return response()->json([
            'account' => $account,
            'subAccounts' => $account->subAccounts,
            'hasSubAccounts' => $account->hasSubAccounts()
        ]);
    }
}