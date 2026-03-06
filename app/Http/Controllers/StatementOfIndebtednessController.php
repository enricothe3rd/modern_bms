<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatementOfIndebtednessRequest;
use App\Http\Requests\UpdateStatementOfIndebtednessRequest;
use App\Models\FiscalYear;
use App\Models\StatementOfIndebtedness;
use App\Services\StatementOfIndebtednessService;
use Illuminate\Http\Request;

class StatementOfIndebtednessController extends Controller
{
    public function __construct(
        protected StatementOfIndebtednessService $statementOfIndebtednessService
    ) {}

    public function index(Request $request)
    {
        $records = $this->statementOfIndebtednessService->getPaginated(
            $request->only(['fiscal_year_id', 'search']),
            15
        );
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();

        return view('statements-of-indebtedness.index', compact('records', 'fiscalYears'));
    }

    public function printByFiscalYear(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year_id' => ['required', 'exists:fiscal_years,id'],
        ]);

        $fiscalYear = FiscalYear::findOrFail($validated['fiscal_year_id']);

        return $this->statementOfIndebtednessService->generateFiscalYearPdf($fiscalYear);
    }

    public function create()
    {
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();
        $currentFiscalYear = FiscalYear::where('is_current', true)->first();

        return view('statements-of-indebtedness.create', compact('fiscalYears', 'currentFiscalYear'));
    }

    public function store(StoreStatementOfIndebtednessRequest $request)
    {
        $this->statementOfIndebtednessService->create($request->validated());

        return redirect()->route('statements-of-indebtedness.index')
            ->with('success', 'Statement of indebtedness created successfully.');
    }

    public function show(StatementOfIndebtedness $statementOfIndebtedness)
    {
        $statementOfIndebtedness->load('fiscalYear');

        return view('statements-of-indebtedness.show', [
            'record' => $statementOfIndebtedness,
        ]);
    }

    public function printView(StatementOfIndebtedness $statementOfIndebtedness)
    {
        return $this->statementOfIndebtednessService->generateSinglePdf($statementOfIndebtedness);
    }

    public function edit(StatementOfIndebtedness $statementOfIndebtedness)
    {
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();

        return view('statements-of-indebtedness.edit', [
            'record' => $statementOfIndebtedness,
            'fiscalYears' => $fiscalYears,
        ]);
    }

    public function update(UpdateStatementOfIndebtednessRequest $request, StatementOfIndebtedness $statementOfIndebtedness)
    {
        $this->statementOfIndebtednessService->update($statementOfIndebtedness, $request->validated());

        return redirect()->route('statements-of-indebtedness.index')
            ->with('success', 'Statement of indebtedness updated successfully.');
    }

    public function destroy(StatementOfIndebtedness $statementOfIndebtedness)
    {
        $this->statementOfIndebtednessService->delete($statementOfIndebtedness);

        return redirect()->route('statements-of-indebtedness.index')
            ->with('success', 'Statement of indebtedness deleted successfully.');
    }
}
