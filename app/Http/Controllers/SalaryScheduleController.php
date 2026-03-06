<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalaryScheduleRequest;
use App\Http\Requests\UpdateSalaryScheduleRequest;
use App\Models\FiscalYear;
use App\Models\SalarySchedule;
use App\Services\SalaryScheduleService;
use Illuminate\Http\Request;

class SalaryScheduleController extends Controller
{
    public function __construct(
        protected SalaryScheduleService $service
    ) {}

    public function index(Request $request)
    {
        $records = $this->service->getPaginated($request->only(['fiscal_year_id', 'search']));
        $fiscalYears = $this->service->getFiscalYears();

        return view('salary-schedules.index', compact('records', 'fiscalYears'));
    }

    public function create()
    {
        $fiscalYears = $this->service->getFiscalYears();
        $currentFiscalYear = $this->service->getCurrentFiscalYear();
        $defaultGrid = $this->service->defaultGrid();

        return view('salary-schedules.create', compact('fiscalYears', 'currentFiscalYear', 'defaultGrid'));
    }

    public function store(StoreSalaryScheduleRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('salary-schedules.index')
            ->with('success', 'Salary schedule created successfully.');
    }

    public function show(SalarySchedule $salarySchedule)
    {
        $record = $this->service->loadFull($salarySchedule);
        $grid = $this->service->gridData($record);

        return view('salary-schedules.show', compact('record', 'grid'));
    }

    public function edit(SalarySchedule $salarySchedule)
    {
        $record = $this->service->loadFull($salarySchedule);
        $fiscalYears = $this->service->getFiscalYears();
        $grid = $this->service->gridData($record);

        return view('salary-schedules.edit', compact('record', 'fiscalYears', 'grid'));
    }

    public function update(UpdateSalaryScheduleRequest $request, SalarySchedule $salarySchedule)
    {
        $this->service->update($salarySchedule, $request->validated());

        return redirect()->route('salary-schedules.index')
            ->with('success', 'Salary schedule updated successfully.');
    }

    public function destroy(SalarySchedule $salarySchedule)
    {
        $this->service->delete($salarySchedule);

        return redirect()->route('salary-schedules.index')
            ->with('success', 'Salary schedule deleted successfully.');
    }

    public function printView(SalarySchedule $salarySchedule)
    {
        return $this->service->generateSinglePdf($salarySchedule);
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
