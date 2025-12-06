<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubAccountService;
use App\Http\Resources\SubAccountResource;
use App\Http\Requests\SubAccountRequest;

class SubAccountController extends Controller
{
    protected $service;

    public function __construct(SubAccountService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $subAccounts = $this->service->getAllSubAccounts();
        $accounts = $this->service->getAllAccounts();

        if ($request->wantsJson()) {
            return SubAccountResource::collection($subAccounts);
        }

        return view('sub-accounts.index', compact('subAccounts', 'accounts'));
    }

    public function store(SubAccountRequest $request)
    {
        $subAccount = $this->service->createSubAccount($request->validated());

        if ($request->wantsJson()) {
            return new SubAccountResource($subAccount);
        }

        return redirect()->back()->with('success', 'Sub-account created successfully!');
    }

    public function edit($id)
    {
        $subAccount = $this->service->findSubAccount($id);
        $subAccounts = $this->service->getAllSubAccounts();
        $accounts = $this->service->getAllAccounts();

        return view('sub-accounts.index', compact('subAccounts', 'accounts', 'subAccount'));
    }

    public function update(SubAccountRequest $request, $id)
    {
        $subAccount = $this->service->updateSubAccount($id, $request->validated());

        if ($request->wantsJson()) {
            return new SubAccountResource($subAccount);
        }

        return redirect()->route('sub-accounts.index')->with('success', 'Sub-account updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $this->service->deleteSubAccount($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Sub-account deleted successfully']);
        }

        return redirect()->route('sub-accounts.index')->with('success', 'Sub-account deleted successfully!');
    }
}
