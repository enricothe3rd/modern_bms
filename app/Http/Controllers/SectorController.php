<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Http\Requests\SectorRequest;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sectors = Sector::withCount(['departments', 'aipCodes'])->orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json($sectors);
        }

        return view('sectors.index', compact('sectors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectorRequest $request)
    {
        $sector = Sector::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sector created successfully',
                'sector' => $sector
            ], 201);
        }

        return redirect()->route('sectors.index')
            ->with('success', 'Sector created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $sector = Sector::findOrFail($id);
        
        if ($request->wantsJson()) {
            return response()->json($sector);
        }

        // For web requests, redirect to index with edit parameter
        return redirect()->route('sectors.index')->with('editSector', $sector);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectorRequest $request, $id)
    {
        $sector = Sector::findOrFail($id);
        $sector->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sector updated successfully',
                'sector' => $sector
            ]);
        }

        return redirect()->route('sectors.index')
            ->with('success', 'Sector updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $sector = Sector::findOrFail($id);
        
        // Check if sector has departments
        if ($sector->departments()->count() > 0) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Cannot delete sector that has departments assigned to it.'
                ], 422);
            }

            return redirect()->route('sectors.index')
                ->with('error', 'Cannot delete sector that has departments assigned to it.');
        }

        $sector->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Sector deleted successfully']);
        }

        return redirect()->route('sectors.index')
            ->with('success', 'Sector deleted successfully!');
    }


}