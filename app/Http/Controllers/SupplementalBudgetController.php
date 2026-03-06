<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplementalBudgetRequest;
use App\Models\SupplementalBudget;
use App\Models\Department;
use App\Models\ExpenseType;
use App\Models\Account;
use App\Models\SubAccount;
use App\Services\SupplementalBudgetService;
use Illuminate\Http\Request;

class SupplementalBudgetController extends Controller
{
    public function __construct(
        protected SupplementalBudgetService $supplementalBudgetService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['fiscal_year_id', 'group', 'status']);
        $supplementalBudgets = $this->supplementalBudgetService->getAllSupplementalBudgets($filters);

        $fiscalYears = \App\Models\FiscalYear::orderBy('year', 'desc')->get();
        $groups = $this->supplementalBudgetService->getAvailableGroups();

        return view('supplemental-budgets.index', compact('supplementalBudgets', 'fiscalYears', 'groups'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $expenseTypes = ExpenseType::orderBy('description')->get();
        $accounts = Account::orderBy('description')->get();
        $subAccounts = SubAccount::orderBy('description')->get();
        $fundTypes = \App\Models\FundType::orderBy('name')->get();
        $fiscalYears = \App\Models\FiscalYear::where('is_active', true)->orderBy('year', 'desc')->get();
        $currentFiscalYear = \App\Models\FiscalYear::where('is_current', true)->first();

        return view('supplemental-budgets.create', compact('departments', 'expenseTypes', 'accounts', 'subAccounts', 'fundTypes', 'fiscalYears', 'currentFiscalYear'));
    }

    public function store(SupplementalBudgetRequest $request)
    {
        try {
            $this->supplementalBudgetService->createSupplementalBudget($request->validated());

            return redirect()->route('supplemental-budgets.index')
                ->with('success', 'Supplemental budget created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating supplemental budget: ' . $e->getMessage());
        }
    }

    public function show(SupplementalBudget $supplementalBudget)
    {
        $supplementalBudget = $this->supplementalBudgetService->getSupplementalBudgetById($supplementalBudget->id);

        if (!$supplementalBudget) {
            return redirect()->route('supplemental-budgets.index')
                ->with('error', 'Supplemental budget not found.');
        }

        return view('supplemental-budgets.show', compact('supplementalBudget'));
    }

    public function edit(SupplementalBudget $supplementalBudget)
    {
        if (!$this->supplementalBudgetService->canEdit($supplementalBudget)) {
            return redirect()->route('supplemental-budgets.show', $supplementalBudget)
                ->with('error', 'Only draft budgets can be edited.');
        }

        $supplementalBudget->load(['items', 'fiscalYear']);
        $departments = Department::orderBy('name')->get();
        $expenseTypes = ExpenseType::orderBy('description')->get();
        $accounts = Account::orderBy('description')->get();
        $subAccounts = SubAccount::orderBy('description')->get();
        $fundTypes = \App\Models\FundType::orderBy('name')->get();

        return view('supplemental-budgets.edit', compact('supplementalBudget', 'departments', 'expenseTypes', 'accounts', 'subAccounts', 'fundTypes'));
    }

    public function update(SupplementalBudgetRequest $request, SupplementalBudget $supplementalBudget)
    {
        try {
            $this->supplementalBudgetService->updateSupplementalBudget($supplementalBudget, $request->validated());

            return redirect()->route('supplemental-budgets.show', $supplementalBudget)
                ->with('success', 'Supplemental budget updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating supplemental budget: ' . $e->getMessage());
        }
    }

    public function destroy(SupplementalBudget $supplementalBudget)
    {
        try {
            $this->supplementalBudgetService->deleteSupplementalBudget($supplementalBudget);

            return redirect()->route('supplemental-budgets.index')
                ->with('success', 'Supplemental budget deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('supplemental-budgets.index')
                ->with('error', 'Error deleting supplemental budget: ' . $e->getMessage());
        }
    }

    public function submit(SupplementalBudget $supplementalBudget)
    {
        try {
            $this->supplementalBudgetService->submitForApproval($supplementalBudget);

            return redirect()->route('supplemental-budgets.show', $supplementalBudget)
                ->with('success', 'Supplemental budget submitted for approval.');
        } catch (\Exception $e) {
            return redirect()->route('supplemental-budgets.show', $supplementalBudget)
                ->with('error', 'Error submitting supplemental budget: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, SupplementalBudget $supplementalBudget)
    {
        try {
            $this->supplementalBudgetService->approve($supplementalBudget, $request->remarks);

            return redirect()->route('supplemental-budgets.show', $supplementalBudget)
                ->with('success', 'Supplemental budget approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('supplemental-budgets.show', $supplementalBudget)
                ->with('error', 'Error approving supplemental budget: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, SupplementalBudget $supplementalBudget)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        try {
            $this->supplementalBudgetService->reject($supplementalBudget, $request->remarks);

            return redirect()->route('supplemental-budgets.show', $supplementalBudget)
                ->with('success', 'Supplemental budget rejected.');
        } catch (\Exception $e) {
            return redirect()->route('supplemental-budgets.show', $supplementalBudget)
                ->with('error', 'Error rejecting supplemental budget: ' . $e->getMessage());
        }
    }

    public function getSubAccounts(Request $request)
    {
        $accountId = $request->get('account_id');
        $subAccounts = SubAccount::where('account_id', $accountId)->orderBy('description')->get();
        
        return response()->json($subAccounts);
    }
}