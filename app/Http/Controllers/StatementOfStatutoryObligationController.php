<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatementOfStatutoryObligationRequest;
use App\Http\Requests\UpdateStatementOfStatutoryObligationRequest;
use App\Models\FiscalYear;
use App\Models\StatementOfStatutoryObligation;
use App\Services\StatementOfStatutoryObligationService;
use Illuminate\Http\Request;

class StatementOfStatutoryObligationController extends Controller
{
    public function __construct(
        protected StatementOfStatutoryObligationService $service
    ) {}

    public function index(Request $request)
    {
        $records = $this->service->getPaginated($request->only(['fiscal_year_id', 'search']));
        $fiscalYears = $this->service->getFiscalYears();

        return view('statements-of-statutory-obligations.index', compact('records', 'fiscalYears'));
    }

    public function create()
    {
        $fiscalYears = $this->service->getFiscalYears();
        $currentFiscalYear = $this->service->getCurrentFiscalYear();

        return view('statements-of-statutory-obligations.create', compact('fiscalYears', 'currentFiscalYear'));
    }

    public function store(StoreStatementOfStatutoryObligationRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('statements-of-statutory-obligations.index')
            ->with('success', 'Statement of statutory obligations created successfully.');
    }

    public function show(StatementOfStatutoryObligation $statementOfStatutoryObligation)
    {
        $record = $this->service->loadFull($statementOfStatutoryObligation);
        return view('statements-of-statutory-obligations.show', compact('record'));
    }

    public function edit(StatementOfStatutoryObligation $statementOfStatutoryObligation)
    {
        $record = $this->service->loadFull($statementOfStatutoryObligation);
        $fiscalYears = $this->service->getFiscalYears();
        return view('statements-of-statutory-obligations.edit', compact('record', 'fiscalYears'));
    }

    public function update(UpdateStatementOfStatutoryObligationRequest $request, StatementOfStatutoryObligation $statementOfStatutoryObligation)
    {
        $this->service->update($statementOfStatutoryObligation, $request->validated());

        return redirect()->route('statements-of-statutory-obligations.index')
            ->with('success', 'Statement of statutory obligations updated successfully.');
    }

    public function destroy(StatementOfStatutoryObligation $statementOfStatutoryObligation)
    {
        $this->service->delete($statementOfStatutoryObligation);

        return redirect()->route('statements-of-statutory-obligations.index')
            ->with('success', 'Statement of statutory obligations deleted successfully.');
    }

    public function printView(StatementOfStatutoryObligation $statementOfStatutoryObligation)
    {
        return $this->service->generateSinglePdf($statementOfStatutoryObligation);
    }

    public function printByFiscalYear(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year_id' => ['required', 'exists:fiscal_years,id'],
        ]);

        $fiscalYear = FiscalYear::findOrFail($validated['fiscal_year_id']);
        return $this->service->generateFiscalYearPdf($fiscalYear);
    }
}
