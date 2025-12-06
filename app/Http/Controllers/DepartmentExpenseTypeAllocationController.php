<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExpenseType;
use App\Models\Account;
use App\Models\SubAccount;
use App\Models\DepartmentExpenseTypeAllocation;
use Illuminate\Http\Request;

class DepartmentExpenseTypeAllocationController extends Controller
{
    public function index($departmentId, $expenseTypeId)
    {
        $department = Department::with('sector')->findOrFail($departmentId);
        $expenseType = ExpenseType::findOrFail($expenseTypeId);
        
        $allocations = DepartmentExpenseTypeAllocation::with(['account', 'subAccount'])
            ->where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->get();

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

    public function store(Request $request, $departmentId, $expenseTypeId)
    {
        // Clean the amount by removing commas before validation
        $request->merge([
            'amount' => str_replace(',', '', $request->input('amount', ''))
        ]);

        $validatedData = $request->validate([
            'account_type' => 'required|in:account,sub_account',
            'account_id' => 'required_if:account_type,account|nullable|exists:accounts,id',
            'sub_account_id' => 'required_if:account_type,sub_account|nullable|exists:sub_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255'
        ]);

        $department = Department::findOrFail($departmentId);
        $expenseType = ExpenseType::findOrFail($expenseTypeId);

        // Additional validation: Prevent allocation to accounts with sub-accounts
        if ($validatedData['account_type'] === 'account' && $validatedData['account_id']) {
            $account = Account::findOrFail($validatedData['account_id']);
            if (!$account->canBeAllocatedTo()) {
                return redirect()->back()
                    ->with('error', 'Cannot allocate to this account because it has sub-accounts. Please select one of its sub-accounts instead.')
                    ->withInput();
            }
        }

        // Additional validation: Verify sub-account belongs to a valid parent account
        if ($validatedData['account_type'] === 'sub_account' && $validatedData['sub_account_id']) {
            $subAccount = SubAccount::with('account')->findOrFail($validatedData['sub_account_id']);
            if (!$subAccount->account) {
                return redirect()->back()
                    ->with('error', 'Selected sub-account does not have a valid parent account.');
            }
        }

        // Check for duplicate allocation
        $existingAllocation = DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->where('account_id', $validatedData['account_type'] === 'account' ? $validatedData['account_id'] : null)
            ->where('sub_account_id', $validatedData['account_type'] === 'sub_account' ? $validatedData['sub_account_id'] : null)
            ->first();

        if ($existingAllocation) {
            return redirect()->back()
                ->with('error', 'An allocation for this account already exists. Please edit the existing allocation instead.');
        }

        // Create new allocation
        DepartmentExpenseTypeAllocation::create([
            'department_id' => $departmentId,
            'expense_type_id' => $expenseTypeId,
            'account_id' => $validatedData['account_type'] === 'account' ? $validatedData['account_id'] : null,
            'sub_account_id' => $validatedData['account_type'] === 'sub_account' ? $validatedData['sub_account_id'] : null,
            'amount' => $validatedData['amount'],
            'description' => $validatedData['description'] ?? null
        ]);

        return redirect()->route('department-expense-type-allocations.index', [$departmentId, $expenseTypeId])
            ->with('success', 'Budget allocation added successfully!');
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

    public function update(Request $request, $departmentId, $expenseTypeId, $allocationId)
    {
        // Clean the amount by removing commas before validation
        $request->merge([
            'amount' => str_replace(',', '', $request->input('amount', ''))
        ]);

        $validatedData = $request->validate([
            'account_type' => 'required|in:account,sub_account',
            'account_id' => 'required_if:account_type,account|nullable|exists:accounts,id',
            'sub_account_id' => 'required_if:account_type,sub_account|nullable|exists:sub_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255'
        ]);

        $allocation = DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->findOrFail($allocationId);

        // Additional validation: Prevent allocation to accounts with sub-accounts
        if ($validatedData['account_type'] === 'account' && $validatedData['account_id']) {
            $account = Account::findOrFail($validatedData['account_id']);
            if (!$account->canBeAllocatedTo()) {
                return redirect()->back()
                    ->with('error', 'Cannot allocate to this account because it has sub-accounts. Please select one of its sub-accounts instead.')
                    ->withInput();
            }
        }

        // Check for duplicate allocation (excluding current allocation)
        $existingAllocation = DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->where('account_id', $validatedData['account_type'] === 'account' ? $validatedData['account_id'] : null)
            ->where('sub_account_id', $validatedData['account_type'] === 'sub_account' ? $validatedData['sub_account_id'] : null)
            ->where('id', '!=', $allocationId)
            ->first();

        if ($existingAllocation) {
            return redirect()->back()
                ->with('error', 'An allocation for this account already exists.');
        }

        // Update allocation
        $allocation->update([
            'account_id' => $validatedData['account_type'] === 'account' ? $validatedData['account_id'] : null,
            'sub_account_id' => $validatedData['account_type'] === 'sub_account' ? $validatedData['sub_account_id'] : null,
            'amount' => $validatedData['amount'],
            'description' => $validatedData['description'] ?? null
        ]);

        return redirect()->route('department-expense-type-allocations.index', [$departmentId, $expenseTypeId])
            ->with('success', 'Budget allocation updated successfully!');
    }

    public function destroy($departmentId, $expenseTypeId, $allocationId)
    {
        $allocation = DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->findOrFail($allocationId);
        
        $allocation->delete();

        return redirect()->route('department-expense-type-allocations.index', [$departmentId, $expenseTypeId])
            ->with('success', 'Allocation removed successfully!');
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