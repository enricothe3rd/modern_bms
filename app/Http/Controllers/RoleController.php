<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Http\Resources\RoleResource;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $roles = $this->service->getAllRoles();

        if ($request->wantsJson()) {
            return RoleResource::collection($roles);
        }

        return view('roles.index', compact('roles'));
    }

    public function store(RoleRequest $request)
    {
        $role = $this->service->createRole($request->validated());

        if ($request->wantsJson()) {
            return new RoleResource($role);
        }

        return redirect()->back()->with('success', 'Role created successfully!');
    }

    public function edit($id)
    {
        $role = $this->service->findRole($id);
        $roles = $this->service->getAllRoles();

        return view('roles.index', compact('roles', 'role'));
    }

    public function update(RoleRequest $request, $id)
    {
        $role = $this->service->updateRole($id, $request->validated());

        if ($request->wantsJson()) {
            return new RoleResource($role);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $this->service->deleteRole($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Role deleted successfully']);
        }

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }
}
