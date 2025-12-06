<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class DepartmentExpenseTypeController extends Controller
{
    public function index($departmentId)
    {
        $department = Department::with(['expenseTypes' => function($query) {
            $query->withPivot(['ppa_code', 'date_issued', 'status']);
        }, 'sector'])->findOrFail($departmentId);
        $allExpenseTypes = ExpenseType::all();
        $assignedExpenseTypeIds = $department->expenseTypes->pluck('id')->toArray();

        return view('department-expense-types.index', compact('department', 'allExpenseTypes', 'assignedExpenseTypeIds'));
    }

    public function store(Request $request, $departmentId)
    {
        $request->validate([
            'expense_type_ids' => 'required|array',
            'expense_type_ids.*' => 'exists:expense_types,id'
        ]);

        $department = Department::findOrFail($departmentId);
        
        // Sync the expense types (this will remove old ones and add new ones)
        $department->expenseTypes()->sync($request->expense_type_ids);

        return redirect()->route('department-expense-types.index', $departmentId)
            ->with('success', 'Expense types assigned successfully!');
    }

    public function updatePpaInfo(Request $request, $departmentId, $expenseTypeId)
    {
        $request->validate([
            'ppa_code' => 'required|string|in:100,200,300,400,500,600,700,800,900,1000,1100,1200,1300,1400,1500,1600,1700,1800,1900,2000,2100,2200,2300,2400,2500,2600,2700,2800,2900,3000,3100,3200,3300,3400,3500,3600,3700,3800,3900,4000,4100,4200,4300,4400,4500,4600,4700,4800,4900,5000',
            'date_issued' => 'required|date'
        ]);

        $department = Department::findOrFail($departmentId);
        
        // Update the pivot table with PPA info
        $department->expenseTypes()->updateExistingPivot($expenseTypeId, [
            'ppa_code' => $request->ppa_code,
            'date_issued' => $request->date_issued,
            'status' => 'saved'
        ]);

        return redirect()->route('department-expense-types.index', $departmentId)
            ->with('success', 'PPA information saved successfully!');
    }

    public function releasePpa(Request $request, $departmentId, $expenseTypeId)
    {
        $department = Department::findOrFail($departmentId);
        
        // Update status to released
        $department->expenseTypes()->updateExistingPivot($expenseTypeId, [
            'status' => 'released'
        ]);

        return redirect()->route('department-expense-types.index', $departmentId)
            ->with('success', 'PPA released successfully!');
    }

    public function unreleasePpa(Request $request, $departmentId, $expenseTypeId)
    {
        // Check if user has permission to unreleased PPA
        if (!auth()->user()->canUnreleasedPpa()) {
            return redirect()->route('department-expense-types.index', $departmentId)
                ->with('error', 'You do not have permission to unreleased PPAs.');
        }

        $department = Department::findOrFail($departmentId);
        
        // Update status back to saved
        $department->expenseTypes()->updateExistingPivot($expenseTypeId, [
            'status' => 'saved'
        ]);

        return redirect()->route('department-expense-types.index', $departmentId)
            ->with('success', 'PPA unreleased successfully! Status changed back to Saved.');
    }

    public function destroy($departmentId, $expenseTypeId)
    {
        $department = Department::findOrFail($departmentId);
        $department->expenseTypes()->detach($expenseTypeId);

        return redirect()->route('department-expense-types.index', $departmentId)
            ->with('success', 'Expense type removed successfully!');
    }
}