<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountService;
use App\Http\Resources\AccountResource;
use App\Http\Requests\AccountRequest;

class AccountController extends Controller
{
    protected $service;

    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $accounts = $this->service->getAllAccounts();

        if ($request->wantsJson()) {
            return AccountResource::collection($accounts);
        }

        return view('accounts.index', compact('accounts'));
    }

    public function store(AccountRequest $request)
    {
        $account = $this->service->createAccount($request->validated());

        if ($request->wantsJson()) {
            return new AccountResource($account);
        }

        return redirect()->back()->with('success', 'Account created successfully!');
    }

    public function edit($id)
    {
        $account = $this->service->findAccount($id);
        $accounts = $this->service->getAllAccounts();

        return view('accounts.index', compact('accounts', 'account'));
    }

    public function update(AccountRequest $request, $id)
    {
        $account = $this->service->updateAccount($id, $request->validated());

        if ($request->wantsJson()) {
            return new AccountResource($account);
        }

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $this->service->deleteAccount($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Account deleted successfully']);
        }

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully!');
    }
}
