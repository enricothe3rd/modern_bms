<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Services\SectorService;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\DepartmentRequest;

class DepartmentController extends Controller
{
    protected $service;
    protected $sectorService;

    public function __construct(DepartmentService $service, SectorService $sectorService)
    {
        $this->service = $service;
        $this->sectorService = $sectorService;
    }

    public function index(Request $request)
    {
        $departments = $this->service->getAllDepartments();
        $sectors = $this->sectorService->getAllSectors();

        if ($request->wantsJson()) {
            return DepartmentResource::collection($departments);
        }

        return view('departments.index', compact('departments', 'sectors'));
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
        $sectors = $this->sectorService->getAllSectors();

        return view('departments.index', compact('departments', 'sectors', 'department'));
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
