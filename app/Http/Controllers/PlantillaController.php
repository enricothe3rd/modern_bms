<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantillaRequest;
use App\Http\Requests\UpdatePlantillaRequest;
use App\Models\FiscalYear;
use App\Models\Plantilla;
use App\Services\PlantillaService;
use Illuminate\Http\Request;

class PlantillaController extends Controller
{
    public function __construct(
        protected PlantillaService $service
    ) {}

    public function index(Request $request)
    {
        $records = $this->service->getPaginated($request->only(['department_id', 'fiscal_year_id', 'search']));
        $departments = $this->service->getDepartments();
        $fiscalYears = $this->service->getFiscalYears();

        return view('plantillas.index', compact('records', 'departments', 'fiscalYears'));
    }

    public function create()
    {
        $departments = $this->service->getDepartments();
        $fiscalYears = $this->service->getFiscalYears();
        $currentFiscalYear = $this->service->getCurrentFiscalYear();
        $salarySchedules = $this->service->getSalarySchedules();

        return view('plantillas.create', compact('departments', 'fiscalYears', 'currentFiscalYear', 'salarySchedules'));
    }

    public function store(StorePlantillaRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('plantillas.index')->with('success', 'Plantilla created successfully.');
    }

    public function show(Plantilla $plantilla)
    {
        $record = $this->service->loadFull($plantilla);
        return view('plantillas.show', compact('record'));
    }

    public function edit(Plantilla $plantilla)
    {
        $record = $this->service->loadFull($plantilla);
        $departments = $this->service->getDepartments();
        $fiscalYears = $this->service->getFiscalYears();
        $salarySchedules = $this->service->getSalarySchedules();

        return view('plantillas.edit', compact('record', 'departments', 'fiscalYears', 'salarySchedules'));
    }

    public function update(UpdatePlantillaRequest $request, Plantilla $plantilla)
    {
        $this->service->update($plantilla, $request->validated());
        return redirect()->route('plantillas.index')->with('success', 'Plantilla updated successfully.');
    }

    public function destroy(Plantilla $plantilla)
    {
        $this->service->delete($plantilla);
        return redirect()->route('plantillas.index')->with('success', 'Plantilla deleted successfully.');
    }

    public function printView(Plantilla $plantilla)
    {
        return $this->service->generateSinglePdf($plantilla);
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
