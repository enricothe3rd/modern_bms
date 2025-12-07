<?php

namespace App\Http\Controllers;

use App\Services\UserDepartmentAssignmentService;
use App\Http\Requests\UserDepartmentAssignmentRequest;
use Illuminate\Http\Request;

class UserDepartmentAssignmentController extends Controller
{
    protected $service;

    public function __construct(UserDepartmentAssignmentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $userDepartmentAssignments = $this->service->getAllAssignmentsGroupedByUser();
        $users = $this->service->getAllUsers();
        $departments = $this->service->getAllDepartments();
        $reviewStatuses = \App\Models\ReviewStatus::active()->ordered()->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $userDepartmentAssignments]);
        }

        return view('user-department-assignments.index', compact('userDepartmentAssignments', 'users', 'departments', 'reviewStatuses'));
    }

    public function store(UserDepartmentAssignmentRequest $request)
    {
        $validated = $request->validated();
        
        $assignmentData = [
            'is_active' => $validated['is_active'] ?? true,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'notes' => $validated['notes'] ?? null
        ];

        $result = $this->service->createAssignments(
            $validated['user_id'],
            $validated['department_ids'],
            $assignmentData,
            $validated['review_status_ids'] ?? []
        );

        $createdAssignments = $result['created'];
        $existingDepartments = $result['existing'];

        if (!empty($existingDepartments)) {
            $message = 'Some departments were skipped because the user is already assigned: ' . implode(', ', $existingDepartments);
            if (empty($createdAssignments)) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => $message], 422);
                }
                return redirect()->back()->withErrors(['error' => $message]);
            }
        }

        $successMessage = count($createdAssignments) . ' department assignment(s) created successfully!';
        if (!empty($existingDepartments)) {
            $successMessage .= ' (Some departments were skipped due to existing assignments)';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $createdAssignments,
                'message' => $successMessage,
                'skipped' => $existingDepartments
            ], 201);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    public function edit($id)
    {
        $userDepartmentAssignment = $this->service->findAssignment($id);
        $userDepartmentAssignments = $this->service->getAllAssignmentsGroupedByUser();
        $users = $this->service->getAllUsers();
        $departments = $this->service->getAllDepartments();
        $reviewStatuses = \App\Models\ReviewStatus::active()->ordered()->get();

        return view('user-department-assignments.index', compact('userDepartmentAssignments', 'users', 'departments', 'reviewStatuses', 'userDepartmentAssignment'));
    }

    public function update(UserDepartmentAssignmentRequest $request, $id)
    {
        $validated = $request->validated();
        
        // Determine department ID from either department_ids or department_id
        $departmentId = $validated['department_ids'][0] ?? $validated['department_id'] ?? null;

        $assignmentData = [
            'is_active' => $validated['is_active'] ?? true,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'notes' => $validated['notes'] ?? null
        ];

        try {
            $userDepartmentAssignment = $this->service->updateAssignment(
                $id,
                $validated['user_id'],
                $departmentId,
                $assignmentData,
                $validated['review_status_ids'] ?? []
            );

            if ($request->wantsJson()) {
                return response()->json(['data' => $userDepartmentAssignment]);
            }

            return redirect()->route('user-department-assignments.index')->with('success', 'User department assignment updated successfully!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->service->deleteAssignment($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User department assignment deleted successfully']);
        }

        return redirect()->route('user-department-assignments.index')->with('success', 'User department assignment deleted successfully!');
    }
}
