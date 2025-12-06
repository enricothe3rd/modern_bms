<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ExpenseType;
use App\Models\Account;
use App\Models\SubAccount;
use Illuminate\Http\Request;

class ObligationRequestApiController extends Controller
{
    /**
     * Get departments by fund type
     */
    public function getDepartmentsByFundType($fundTypeId)
    {
        // Get departments that belong to the selected fund type and have expense types allocated
        $departments = Department::where('fund_type_id', $fundTypeId)
            ->whereHas('expenseTypes')
            ->with('sector')
            ->orderBy('name')
            ->get();

        return response()->json($departments);
    }

    /**
     * Get expense types by fund type and department
     */
    public function getExpenseTypesByDepartment($fundTypeId, $departmentId)
    {
        // Get expense types allocated to this department
        $expenseTypes = ExpenseType::whereHas('departments', function ($query) use ($departmentId) {
            $query->where('departments.id', $departmentId);
        })->orderBy('description')->get();

        return response()->json($expenseTypes);
    }

    /**
     * Get accounts and sub-accounts by department and expense type
     * Returns a hierarchical structure with main accounts and their sub-accounts
     */
    public function getAccountsByExpenseType($fundTypeId, $departmentId, $expenseTypeId)
    {
        // Get current year or use request parameter
        $year = request('year', date('Y'));
        
        // Get all allocations for this department + expense type combination for the specified year
        $allocations = \App\Models\DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->where('year', $year)
            ->with(['account', 'subAccount.account'])
            ->get();

        // Build hierarchical structure
        $accountsMap = [];
        $subAccountsByParent = [];
        
        foreach ($allocations as $allocation) {
            if ($allocation->account_id && $allocation->account) {
                // Direct main account allocation (no sub-account)
                $accountId = $allocation->account->id;
                if (!isset($accountsMap[$accountId])) {
                    $accountsMap[$accountId] = [
                        'id' => $allocation->account->id,
                        'code' => $allocation->account->code,
                        'description' => $allocation->account->description,
                        'type' => 'account',
                        'is_selectable' => true, // Will be set to false if has sub-accounts
                        'allocated_amount' => $allocation->amount,
                        'sub_accounts' => []
                    ];
                } else {
                    // Add to allocated amount if multiple allocations exist
                    $accountsMap[$accountId]['allocated_amount'] += $allocation->amount;
                }
            } elseif ($allocation->sub_account_id && $allocation->subAccount) {
                // Sub-account allocation
                $parentAccount = $allocation->subAccount->account;
                if ($parentAccount) {
                    $parentId = $parentAccount->id;
                    
                    // Add parent account if not exists
                    if (!isset($accountsMap[$parentId])) {
                        $accountsMap[$parentId] = [
                            'id' => $parentAccount->id,
                            'code' => $parentAccount->code,
                            'description' => $parentAccount->description,
                            'type' => 'account',
                            'is_selectable' => false, // Parent with sub-accounts is not selectable
                            'allocated_amount' => 0,
                            'sub_accounts' => []
                        ];
                    } else {
                        // Mark existing account as not selectable since it has sub-accounts
                        $accountsMap[$parentId]['is_selectable'] = false;
                    }
                    
                    // Add sub-account
                    $subAccountId = $allocation->subAccount->id;
                    if (!isset($subAccountsByParent[$parentId][$subAccountId])) {
                        $accountsMap[$parentId]['sub_accounts'][] = [
                            'id' => $allocation->subAccount->id,
                            'code' => $allocation->subAccount->code,
                            'description' => $allocation->subAccount->description,
                            'type' => 'sub_account',
                            'parent_id' => $parentId,
                            'allocated_amount' => $allocation->amount
                        ];
                        $subAccountsByParent[$parentId][$subAccountId] = true;
                    }
                }
            }
        }

        return response()->json(array_values($accountsMap));
    }

    /**
     * Get sub-accounts by account, department, and expense type
     */
    public function getSubAccountsByAccount($fundTypeId, $departmentId, $expenseTypeId, $accountId)
    {
        // Get sub-accounts allocated to this department + expense type + account combination
        $allocations = \App\Models\DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->whereNotNull('sub_account_id')
            ->whereHas('subAccount', function ($query) use ($accountId) {
                $query->where('account_id', $accountId);
            })
            ->with('subAccount')
            ->get();

        $subAccounts = $allocations->map(function ($allocation) {
            return [
                'id' => $allocation->subAccount->id,
                'code' => $allocation->subAccount->code,
                'description' => $allocation->subAccount->description,
                'account_id' => $allocation->subAccount->account_id
            ];
        })->unique('id')->values();

        return response()->json($subAccounts);
    }
}
