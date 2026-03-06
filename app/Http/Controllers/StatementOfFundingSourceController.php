<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatementOfFundingSourceRequest;
use App\Http\Requests\UpdateStatementOfFundingSourceRequest;
use App\Models\FiscalYear;
use App\Models\StatementOfFundingSource;
use App\Services\StatementOfFundingSourceService;
use Illuminate\Http\Request;

class StatementOfFundingSourceController extends Controller
{
    public function __construct(
        protected StatementOfFundingSourceService $service
    ) {}

    public function index(Request $request)
    {
        $records = $this->service->getPaginated($request->only(['fiscal_year_id', 'search']));
        $fiscalYears = $this->service->getFiscalYears();

        return view('statements-of-funding-sources.index', compact('records', 'fiscalYears'));
    }

    public function create()
    {
        $fiscalYears = $this->service->getFiscalYears();
        $currentFiscalYear = $this->service->getCurrentFiscalYear();

        return view('statements-of-funding-sources.create', compact('fiscalYears', 'currentFiscalYear'));
    }

    public function store(StoreStatementOfFundingSourceRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('statements-of-funding-sources.index')
            ->with('success', 'Statement of funding sources created successfully.');
    }

    public function show(StatementOfFundingSource $statementOfFundingSource)
    {
        $record = $this->service->loadFull($statementOfFundingSource);

        return view('statements-of-funding-sources.show', compact('record'));
    }

    public function printView(StatementOfFundingSource $statementOfFundingSource)
    {
        return $this->service->generateSinglePdf($statementOfFundingSource);
    }

    public function printByFiscalYear(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year_id' => ['required', 'exists:fiscal_years,id'],
        ]);

        $fiscalYear = FiscalYear::findOrFail($validated['fiscal_year_id']);

        return $this->service->generateFiscalYearPdf($fiscalYear);
    }

    public function edit(StatementOfFundingSource $statementOfFundingSource)
    {
        $record = $this->service->loadFull($statementOfFundingSource);
        $fiscalYears = $this->service->getFiscalYears();

        return view('statements-of-funding-sources.edit', compact('record', 'fiscalYears'));
    }

    public function update(UpdateStatementOfFundingSourceRequest $request, StatementOfFundingSource $statementOfFundingSource)
    {
        $this->service->update($statementOfFundingSource, $request->validated());

        return redirect()->route('statements-of-funding-sources.index')
            ->with('success', 'Statement of funding sources updated successfully.');
    }

    public function destroy(StatementOfFundingSource $statementOfFundingSource)
    {
        $this->service->delete($statementOfFundingSource);

        return redirect()->route('statements-of-funding-sources.index')
            ->with('success', 'Statement of funding sources deleted successfully.');
    }
}
