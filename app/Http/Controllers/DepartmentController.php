<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\DepartmentRequest;

class DepartmentController extends Controller
{
    protected $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $departments = $this->service->getAllDepartments();

        if ($request->wantsJson()) {
            return DepartmentResource::collection($departments);
        }

        return view('departments.index', compact('departments'));
    }

    public function store(DepartmentRequest $request)
    {
        $department = $this->service->createDepartment($request->validated());

        if ($request->wantsJson()) {
            return new DepartmentResource($department);
        }

        return redirect()->back()->with('success', 'Department created successfully!');
    }


    public function edit($id)
    {
        $department = $this->service->findDepartment($id);
        $departments = $this->service->getAllDepartments();

        return view('departments.index', compact('departments', 'department'));
    }

    public function update(DepartmentRequest $request, $id)
    {
        $department = $this->service->updateDepartment($id, $request->validated());

        if ($request->wantsJson()) {
            return new DepartmentResource($department);
        }

        return redirect()->route('departments.index')->with('success', 'Department updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $this->service->deleteDepartment($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Department deleted successfully']);
        }

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully!');
    }
}
