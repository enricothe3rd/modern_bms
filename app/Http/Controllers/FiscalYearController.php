<?php

namespace App\Http\Controllers;

use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FiscalYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->paginate(15);
        
        return view('fiscal-years.index', compact('fiscalYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentYear = date('Y');
        $suggestedYears = [];
        
        // Suggest next 5 years that don't exist yet
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear + $i;
            if (!FiscalYear::where('year', $year)->exists()) {
                $suggestedYears[] = $year;
            }
        }
        
        return view('fiscal-years.create', compact('suggestedYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2050|unique:fiscal_years,year',
            'description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_current' => 'boolean'
        ]);

        $fiscalYear = FiscalYear::create($request->all());

        // If this is set as current, update others
        if ($request->is_current) {
            $fiscalYear->setCurrent();
        }

        return redirect()->route('fiscal-years.index')
            ->with('success', 'Fiscal year created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FiscalYear $fiscalYear)
    {
        return view('fiscal-years.show', compact('fiscalYear'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FiscalYear $fiscalYear)
    {
        return view('fiscal-years.edit', compact('fiscalYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2050|unique:fiscal_years,year,' . $fiscalYear->id,
            'description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_current' => 'boolean'
        ]);

        $fiscalYear->update($request->all());

        // If this is set as current, update others
        if ($request->is_current) {
            $fiscalYear->setCurrent();
        }

        return redirect()->route('fiscal-years.index')
            ->with('success', 'Fiscal year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FiscalYear $fiscalYear)
    {
        // Prevent deletion of current fiscal year
        if ($fiscalYear->is_current) {
            return redirect()->route('fiscal-years.index')
                ->with('error', 'Cannot delete the current fiscal year.');
        }

        $fiscalYear->delete();

        return redirect()->route('fiscal-years.index')
            ->with('success', 'Fiscal year deleted successfully.');
    }

    /**
     * Set a fiscal year as current
     */
    public function setCurrent(FiscalYear $fiscalYear)
    {
        $fiscalYear->setCurrent();

        return redirect()->route('fiscal-years.index')
            ->with('success', 'Fiscal year set as current successfully.');
    }

    /**
     * Generate fiscal years for a range
     */
    public function generate(Request $request)
    {
        $request->validate([
            'start_year' => 'required|integer|min:2020|max:2050',
            'end_year' => 'required|integer|min:2020|max:2050|gte:start_year'
        ]);

        $created = 0;
        for ($year = $request->start_year; $year <= $request->end_year; $year++) {
            if (!FiscalYear::where('year', $year)->exists()) {
                FiscalYear::create(FiscalYear::generateForYear($year));
                $created++;
            }
        }

        return redirect()->route('fiscal-years.index')
            ->with('success', "Generated {$created} fiscal years successfully.");
    }
}