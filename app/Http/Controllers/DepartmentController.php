<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Services\SectorService;
use App\Repositories\FundTypeRepository;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\DepartmentRequest;

class DepartmentController extends Controller
{
    protected $service;
    protected $sectorService;
    protected $fundTypeRepo;

    public function __construct(DepartmentService $service, SectorService $sectorService, FundTypeRepository $fundTypeRepo)
    {
        $this->service = $service;
        $this->sectorService = $sectorService;
        $this->fundTypeRepo = $fundTypeRepo;
    }

    public function index(Request $request)
    {
        $departments = $this->service->getAllDepartments();
        $sectors = $this->sectorService->getAllSectors();
        $fundTypes = $this->fundTypeRepo->all();

        if ($request->wantsJson()) {
            return DepartmentResource::collection($departments);
        }

        return view('departments.index', compact('departments', 'sectors', 'fundTypes'));
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
        $fundTypes = $this->fundTypeRepo->all();

        return view('departments.index', compact('departments', 'sectors', 'fundTypes', 'department'));
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
