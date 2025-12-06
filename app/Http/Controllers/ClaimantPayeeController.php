<?php

namespace App\Http\Controllers;

use App\Services\ClaimantPayeeService;
use App\Http\Requests\ClaimantPayeeRequest;
use Illuminate\Http\Request;

class ClaimantPayeeController extends Controller
{
    protected $service;

    public function __construct(ClaimantPayeeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $claimantPayees = $this->service->getAllClaimantPayees();
        $departments = $this->service->getAllDepartments();
        $payeeCategories = $this->service->getAllPayeeCategories();

        if ($request->wantsJson()) {
            return response()->json(['data' => $claimantPayees]);
        }

        return view('claimant-payees.index', compact('claimantPayees', 'departments', 'payeeCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClaimantPayeeRequest $request)
    {
        $claimantPayee = $this->service->createClaimantPayee($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $claimantPayee,
                'message' => 'Claimant payee created successfully'
            ], 201);
        }

        return redirect()->route('claimant-payees.index')
            ->with('success', 'Claimant payee created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $claimantPayee = $this->service->findClaimantPayee($id);
        $claimantPayees = $this->service->getAllClaimantPayees();
        $departments = $this->service->getAllDepartments();
        $payeeCategories = $this->service->getAllPayeeCategories();

        if ($request->wantsJson()) {
            return response()->json($claimantPayee);
        }

        return view('claimant-payees.index', compact('claimantPayee', 'claimantPayees', 'departments', 'payeeCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClaimantPayeeRequest $request, $id)
    {
        $claimantPayee = $this->service->updateClaimantPayee($id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $claimantPayee,
                'message' => 'Claimant payee updated successfully'
            ]);
        }

        return redirect()->route('claimant-payees.index')
            ->with('success', 'Claimant payee updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $this->service->deleteClaimantPayee($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Claimant payee deleted successfully']);
        }

        return redirect()->route('claimant-payees.index')
            ->with('success', 'Claimant payee deleted successfully!');
    }
}

