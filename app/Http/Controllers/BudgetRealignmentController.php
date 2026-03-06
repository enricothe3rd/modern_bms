<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRealignmentRequest;
use App\Models\BudgetRealignment;
use App\Models\FiscalYear;
use App\Models\FundType;
use App\Models\Department;
use App\Models\ExpenseType;
use App\Models\Account;
use App\Models\SubAccount;
use App\Models\DepartmentExpenseTypeAllocation;
use App\Services\BudgetRealignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BudgetRealignmentController extends Controller
{
    public function __construct(
        private BudgetRealignmentService $budgetRealignmentService
    ) {}

    public function index(Request $request)
    {
        $fiscalYearId = $request->get('fiscal_year_id');
        
        if ($fiscalYearId) {
            $realignments = $this->budgetRealignmentService->getByFiscalYear($fiscalYearId, 15);
        } else {
            $realignments = $this->budgetRealignmentService->getAllPaginated(15);
        }

        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();
        $currentFiscalYear = FiscalYear::where('is_current', true)->first();

        return view('budget-realignments.index', compact('realignments', 'fiscalYears', 'currentFiscalYear', 'fiscalYearId'));
    }

    public function create()
    {
        $fundTypes = FundType::orderBy('description')->get();
        $departments = Department::orderBy('name')->get();
        $expenseTypes = ExpenseType::orderBy('description')->get();
        $accounts = Account::orderBy('description')->get();
        $subAccounts = SubAccount::orderBy('description')->get();
        $fiscalYears = FiscalYear::where('is_active', true)->orderBy('year', 'desc')->get();
        $currentFiscalYear = FiscalYear::where('is_current', true)->first();
        
        return view('budget-realignments.create', compact('fundTypes', 'departments', 'expenseTypes', 'accounts', 'subAccounts', 'fiscalYears', 'currentFiscalYear'));
    }

    public function store(BudgetRealignmentRequest $request)
    {
        try {
            $realignment = $this->budgetRealignmentService->create($request->validated());

            return redirect()->route('budget-realignments.index')
                ->with('success', 'Budget realignment created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(BudgetRealignment $budgetRealignment)
    {
        $budgetRealignment = $this->budgetRealignmentService->findById($budgetRealignment->id);

        if (!$budgetRealignment) {
            return redirect()->route('budget-realignments.index')
                ->with('error', 'Budget realignment not found.');
        }

        return view('budget-realignments.show', compact('budgetRealignment'));
    }

    public function edit(BudgetRealignment $budgetRealignment)
    {
        if (!$budgetRealignment->canEdit()) {
            return redirect()->route('budget-realignments.show', $budgetRealignment)
                ->with('error', 'This realignment cannot be edited in its current status.');
        }

        $budgetRealignment = $this->budgetRealignmentService->findById($budgetRealignment->id);
        $departments = Department::orderBy('name')->get();
        $expenseTypes = ExpenseType::orderBy('description')->get();
        $accounts = Account::orderBy('description')->get();
        $subAccounts = SubAccount::orderBy('description')->get();
        $fiscalYears = FiscalYear::where('is_active', true)->orderBy('year', 'desc')->get();
        $currentFiscalYear = FiscalYear::where('is_current', true)->first();

        return view('budget-realignments.edit', compact('budgetRealignment', 'departments', 'expenseTypes', 'accounts', 'subAccounts', 'fiscalYears', 'currentFiscalYear'));
    }

    public function update(BudgetRealignmentRequest $request, BudgetRealignment $budgetRealignment)
    {
        try {
            $this->budgetRealignmentService->update($budgetRealignment, $request->validated());

            return redirect()->route('budget-realignments.show', $budgetRealignment)
                ->with('success', 'Budget realignment updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(BudgetRealignment $budgetRealignment)
    {
        try {
            $this->budgetRealignmentService->delete($budgetRealignment);

            return redirect()->route('budget-realignments.index')
                ->with('success', 'Budget realignment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('budget-realignments.index')
                ->with('error', $e->getMessage());
        }
    }

    public function submit(BudgetRealignment $budgetRealignment)
    {
        try {
            $this->budgetRealignmentService->submit($budgetRealignment);

            return redirect()->route('budget-realignments.show', $budgetRealignment)
                ->with('success', 'Budget realignment submitted for approval.');
        } catch (\Exception $e) {
            return redirect()->route('budget-realignments.show', $budgetRealignment)
                ->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, BudgetRealignment $budgetRealignment)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:1000'
        ]);

        try {
            if ($request->action === 'approve') {
                $this->budgetRealignmentService->approve($budgetRealignment, $request->remarks);
                $message = 'Budget realignment approved and applied successfully.';
            } else {
                $this->budgetRealignmentService->reject($budgetRealignment, $request->remarks);
                $message = 'Budget realignment rejected.';
            }

            return redirect()->route('budget-realignments.show', $budgetRealignment)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('budget-realignments.show', $budgetRealignment)
                ->with('error', $e->getMessage());
        }
    }

    // API methods for AJAX calls
    public function getAllocations(Request $request)
    {
        $fiscalYearId = $request->fiscal_year_id;
        $fundTypeId = $request->fund_type_id;
        $departmentId = $request->department_id;
        $expenseTypeId = $request->expense_type_id;

        if (!$fiscalYearId) {
            return response()->json(['error' => 'Fiscal year is required'], 400);
        }

        try {
            $allocations = $this->budgetRealignmentService->getAllocationsByFiscalYear(
                $fiscalYearId,
                $fundTypeId,
                $departmentId,
                $expenseTypeId
            );

            return response()->json($allocations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading allocations'], 500);
        }
    }

    public function getAllocationInfo(Request $request)
    {
        $fiscalYearId = $request->fiscal_year_id;
        $departmentId = $request->department_id;
        $expenseTypeId = $request->expense_type_id;
        $accountId = $request->account_id;
        $subAccountId = $request->sub_account_id;

        // Log the request parameters for debugging
        Log::info('getAllocationInfo called with params:', [
            'fiscal_year_id' => $fiscalYearId,
            'department_id' => $departmentId,
            'expense_type_id' => $expenseTypeId,
            'account_id' => $accountId,
            'sub_account_id' => $subAccountId,
        ]);

        if (!$fiscalYearId || !$departmentId || !$expenseTypeId || !$accountId) {
            return response()->json(['error' => 'Required parameters missing'], 400);
        }

        try {
            $allocation = DepartmentExpenseTypeAllocation::where([
                'fiscal_year_id' => $fiscalYearId,
                'department_id' => $departmentId,
                'expense_type_id' => $expenseTypeId,
                'account_id' => $accountId,
                'sub_account_id' => $subAccountId,
            ])->first();

            if (!$allocation) {
                return response()->json([
                    'exists' => false,
                    'allocated_amount' => 0,
                    'obligated_amount' => 0,
                    'available_amount' => 0,
                    'message' => 'No allocation found for this combination'
                ]);
            }

            $obligatedAmount = $allocation->getObligatedAmount();
            $availableAmount = $allocation->getAvailableAmount();

            return response()->json([
                'exists' => true,
                'allocated_amount' => $allocation->amount,
                'obligated_amount' => $obligatedAmount,
                'available_amount' => $availableAmount,
                'allocation_id' => $allocation->id,
                'message' => 'Allocation found'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAllocationInfo: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading allocation info: ' . $e->getMessage()], 500);
        }
    }
}
