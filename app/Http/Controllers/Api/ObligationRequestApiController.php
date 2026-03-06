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
        // Get fiscal year ID from request parameter
        $fiscalYearId = request('fiscal_year_id');
        
        \Log::info('getExpenseTypesByDepartment called', [
            'fund_type_id' => $fundTypeId,
            'department_id' => $departmentId,
            'fiscal_year_id' => $fiscalYearId
        ]);
        
        if (!$fiscalYearId) {
            // Fallback to current fiscal year if not provided
            $currentFiscalYear = \App\Models\FiscalYear::where('is_current', true)->first();
            $fiscalYearId = $currentFiscalYear ? $currentFiscalYear->id : null;
            \Log::info('Using fallback fiscal year', ['fiscal_year_id' => $fiscalYearId]);
        }
        
        if (!$fiscalYearId) {
            \Log::warning('No fiscal year found, returning empty array');
            return response()->json([]);
        }
        
        // Get expense types that have allocations for this department in the selected fiscal year
        $expenseTypes = ExpenseType::whereHas('departmentExpenseTypeAllocations', function ($query) use ($departmentId, $fiscalYearId) {
            $query->where('department_id', $departmentId)
                  ->where('fiscal_year_id', $fiscalYearId);
        })->orderBy('description')->get();

        \Log::info('Found expense types', ['count' => $expenseTypes->count()]);

        return response()->json($expenseTypes);
    }

    /**
     * Get accounts and sub-accounts by department and expense type
     * Returns a hierarchical structure with main accounts and their sub-accounts
     */
    public function getAccountsByExpenseType($fundTypeId, $departmentId, $expenseTypeId)
    {
        // Get fiscal year ID from request parameter
        $fiscalYearId = request('fiscal_year_id');
        
        if (!$fiscalYearId) {
            // Fallback to current fiscal year if not provided
            $currentFiscalYear = \App\Models\FiscalYear::where('is_current', true)->first();
            $fiscalYearId = $currentFiscalYear ? $currentFiscalYear->id : null;
        }
        
        if (!$fiscalYearId) {
            return response()->json([]);
        }
        
        // Get all allocations for this department + expense type combination for the specified fiscal year
        $allocations = \App\Models\DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->with(['account', 'subAccount.account'])
            ->get();

        // Calculate obligated amounts for accounts
        $accountObligations = \App\Models\ObligationRequestItem::whereHas('obligationRequest', function($query) use ($fiscalYearId) {
                $query->where('fiscal_year_id', $fiscalYearId);
            })
            ->where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->whereNotNull('account_id')
            ->whereNull('sub_account_id')
            ->selectRaw('account_id, SUM(amount) as total_obligated')
            ->groupBy('account_id')
            ->pluck('total_obligated', 'account_id');

        // Calculate obligated amounts for sub-accounts
        $subAccountObligations = \App\Models\ObligationRequestItem::whereHas('obligationRequest', function($query) use ($fiscalYearId) {
                $query->where('fiscal_year_id', $fiscalYearId);
            })
            ->where('department_id', $departmentId)
            ->where('expense_type_id', $expenseTypeId)
            ->whereNotNull('sub_account_id')
            ->selectRaw('sub_account_id, SUM(amount) as total_obligated')
            ->groupBy('sub_account_id')
            ->pluck('total_obligated', 'sub_account_id');

        // Build hierarchical structure
        $accountsMap = [];
        $subAccountsByParent = [];
        
        foreach ($allocations as $allocation) {
            if ($allocation->account_id && $allocation->account) {
                // Direct main account allocation (no sub-account)
                $accountId = $allocation->account->id;
                if (!isset($accountsMap[$accountId])) {
                    $obligated = $accountObligations[$accountId] ?? 0;
                    $accountsMap[$accountId] = [
                        'id' => $allocation->account->id,
                        'code' => $allocation->account->code,
                        'description' => $allocation->account->description,
                        'type' => 'account',
                        'is_selectable' => true, // Will be set to false if has sub-accounts
                        'allocated_amount' => $allocation->amount,
                        'obligated_amount' => $obligated,
                        'available_amount' => $allocation->amount - $obligated,
                        'sub_accounts' => []
                    ];
                } else {
                    // Add to allocated amount if multiple allocations exist
                    $accountsMap[$accountId]['allocated_amount'] += $allocation->amount;
                    $accountsMap[$accountId]['available_amount'] = $accountsMap[$accountId]['allocated_amount'] - $accountsMap[$accountId]['obligated_amount'];
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
                            'obligated_amount' => 0,
                            'available_amount' => 0,
                            'sub_accounts' => []
                        ];
                    } else {
                        // Mark existing account as not selectable since it has sub-accounts
                        $accountsMap[$parentId]['is_selectable'] = false;
                    }
                    
                    // Add sub-account
                    $subAccountId = $allocation->subAccount->id;
                    if (!isset($subAccountsByParent[$parentId][$subAccountId])) {
                        $obligated = $subAccountObligations[$subAccountId] ?? 0;
                        $accountsMap[$parentId]['sub_accounts'][] = [
                            'id' => $allocation->subAccount->id,
                            'code' => $allocation->subAccount->code,
                            'description' => $allocation->subAccount->description,
                            'type' => 'sub_account',
                            'parent_id' => $parentId,
                            'allocated_amount' => $allocation->amount,
                            'obligated_amount' => $obligated,
                            'available_amount' => $allocation->amount - $obligated
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

    /**
     * Get expense types by department only (fallback when fund type is not selected)
     */
    public function getExpenseTypesByDepartmentOnly($departmentId)
    {
        // Get fiscal year ID from request parameter
        $fiscalYearId = request('fiscal_year_id');
        
        if (!$fiscalYearId) {
            // Fallback to current fiscal year if not provided
            $currentFiscalYear = \App\Models\FiscalYear::where('is_current', true)->first();
            $fiscalYearId = $currentFiscalYear ? $currentFiscalYear->id : null;
        }
        
        if (!$fiscalYearId) {
            return response()->json([]);
        }
        
        // Get expense types that have allocations for this department in the selected fiscal year (regardless of fund type)
        $expenseTypes = ExpenseType::whereHas('departmentExpenseTypeAllocations', function ($query) use ($departmentId, $fiscalYearId) {
            $query->where('department_id', $departmentId)
                  ->where('fiscal_year_id', $fiscalYearId);
        })->orderBy('description')->get();

        return response()->json($expenseTypes);
    }

    /**
     * Get all obligation requests for real-time updates
     */
    public function getObligationRequests()
    {
        $obligationRequests = \App\Models\ObligationRequest::with(['department', 'claimantPayee'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($obr) {
                return [
                    'id' => $obr->id,
                    'obr_number' => $obr->obr_number,
                    'department_name' => $obr->department->name,
                    'claimant_payee_name' => $obr->claimantPayee->name,
                    'obligation_date' => $obr->obligation_date->format('M d, Y'),
                    'total_amount' => $obr->total_amount,
                    'status' => $obr->status,
                    'created_at' => $obr->created_at->toIso8601String(),
                    'updated_at' => $obr->updated_at->toIso8601String(),
                ];
            });

        return response()->json($obligationRequests);
    }
}
