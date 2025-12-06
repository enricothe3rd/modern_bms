<?php

namespace App\Http\Controllers;

use App\Services\RolePermissionService;
use App\Http\Requests\RolePermissionRequest;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    protected $service;

    public function __construct(RolePermissionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $roles = $this->service->getAllRolesWithPermissions();
        $availablePermissions = $this->service->getAvailablePermissions();
        $groupedPermissions = $this->service->getGroupedPermissions();

        return view('role-permissions.index', compact('roles', 'availablePermissions', 'groupedPermissions'));
    }

    public function store(RolePermissionRequest $request)
    {
        $this->service->updateRolePermissions($request->role_id, $request->permissions);

        return redirect()->route('role-permissions.index')
            ->with('success', 'Role permissions updated successfully!');
    }

    public function show($roleId)
    {
        $role = $this->service->getRoleWithPermissions($roleId);
        $availablePermissions = $this->service->getAvailablePermissions();
        $groupedPermissions = $this->service->getGroupedPermissions();
        $assignedPermissions = $this->service->getAssignedPermissions($roleId);

        return response()->json([
            'role' => $role,
            'availablePermissions' => $availablePermissions,
            'groupedPermissions' => $groupedPermissions,
            'assignedPermissions' => $assignedPermissions
        ]);
    }
}