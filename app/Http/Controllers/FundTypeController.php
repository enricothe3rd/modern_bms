<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FundType;

class FundTypeController extends Controller
{
    public function index(Request $request)
    {
        $fundTypes = FundType::latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $fundTypes]);
        }

        return view('fund-types.index', compact('fundTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $fundType = FundType::create([
            'description' => $request->description
        ]);

        if ($request->wantsJson()) {
            return response()->json(['data' => $fundType], 201);
        }

        return redirect()->back()->with('success', 'Fund type created successfully!');
    }

    public function edit(Request $request, $id)
    {
        $fundType = FundType::findOrFail($id);
        
        if ($request->wantsJson()) {
            return response()->json(['fundType' => $fundType]);
        }
        
        $fundTypes = FundType::latest()->get();
        return view('fund-types.index', compact('fundTypes', 'fundType'));
    }

    public function update(Request $request, $id)
    {
        $fundType = FundType::findOrFail($id);

        $request->validate([
            'description' => 'required|string|max:255'
        ]);

        $fundType->update([
            'description' => $request->description
        ]);

        if ($request->wantsJson()) {
            return response()->json(['data' => $fundType]);
        }

        return redirect()->route('fund-types.index')->with('success', 'Fund type updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $fundType = FundType::findOrFail($id);
        $fundType->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Fund type deleted successfully']);
        }

        return redirect()->route('fund-types.index')->with('success', 'Fund type deleted successfully!');
    }
}
