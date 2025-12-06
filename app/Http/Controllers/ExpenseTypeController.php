<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExpenseTypeService;
use App\Http\Resources\ExpenseTypeResource;
use App\Http\Requests\ExpenseTypeRequest;

class ExpenseTypeController extends Controller
{
    protected $service;

    public function __construct(ExpenseTypeService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $expenseTypes = $this->service->getAllExpenseTypes();

        if ($request->wantsJson()) {
            return ExpenseTypeResource::collection($expenseTypes);
        }

        return view('expense-types.index', compact('expenseTypes'));
    }

    public function store(ExpenseTypeRequest $request)
    {
        $expenseType = $this->service->createExpenseType($request->validated());

        if ($request->wantsJson()) {
            return new ExpenseTypeResource($expenseType);
        }

        return redirect()->back()->with('success', 'Expense type created successfully!');
    }

    public function edit($id)
    {
        $expenseType = $this->service->findExpenseType($id);
        $expenseTypes = $this->service->getAllExpenseTypes();

        return view('expense-types.index', compact('expenseTypes', 'expenseType'));
    }

    public function update(ExpenseTypeRequest $request, $id)
    {
        $expenseType = $this->service->updateExpenseType($id, $request->validated());

        if ($request->wantsJson()) {
            return new ExpenseTypeResource($expenseType);
        }

        return redirect()->route('expense-types.index')->with('success', 'Expense type updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $this->service->deleteExpenseType($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Expense type deleted successfully']);
        }

        return redirect()->route('expense-types.index')->with('success', 'Expense type deleted successfully!');
    }
}
