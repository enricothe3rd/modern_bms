<?php

namespace App\Http\Controllers;

use App\Services\PayeeCategoryService;
use App\Http\Requests\PayeeCategoryRequest;
use Illuminate\Http\Request;

class PayeeCategoryController extends Controller
{
    protected $service;

    public function __construct(PayeeCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payeeCategories = $this->service->getAllPayeeCategories();

        if ($request->wantsJson()) {
            return response()->json(['data' => $payeeCategories]);
        }

        return view('payee-categories.index', compact('payeeCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PayeeCategoryRequest $request)
    {
        $payeeCategory = $this->service->createPayeeCategory($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $payeeCategory,
                'message' => 'Payee category created successfully'
            ], 201);
        }

        return redirect()->route('payee-categories.index')
            ->with('success', 'Payee category created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $payeeCategory = $this->service->findPayeeCategory($id);
        $payeeCategories = $this->service->getAllPayeeCategories();

        if ($request->wantsJson()) {
            return response()->json($payeeCategory);
        }

        return view('payee-categories.index', compact('payeeCategory', 'payeeCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PayeeCategoryRequest $request, $id)
    {
        $payeeCategory = $this->service->updatePayeeCategory($id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $payeeCategory,
                'message' => 'Payee category updated successfully'
            ]);
        }

        return redirect()->route('payee-categories.index')
            ->with('success', 'Payee category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $this->service->deletePayeeCategory($id);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Payee category deleted successfully']);
            }

            return redirect()->route('payee-categories.index')
                ->with('success', 'Payee category deleted successfully!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->route('payee-categories.index')
                ->with('error', $e->getMessage());
        }
    }
}
