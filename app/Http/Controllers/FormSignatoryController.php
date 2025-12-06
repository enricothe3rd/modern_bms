<?php

namespace App\Http\Controllers;

use App\Services\FormSignatoryService;
use App\Http\Requests\FormSignatoryRequest;
use Illuminate\Http\Request;

class FormSignatoryController extends Controller
{
    protected $service;

    public function __construct(FormSignatoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $formSignatories = $this->service->getAllSignatoriesGrouped();
        $forms = $this->service->getAllForms();
        $departments = $this->service->getAllDepartments();
        $users = $this->service->getAllUsers();

        if ($request->wantsJson()) {
            return response()->json(['data' => $formSignatories]);
        }

        return view('form-signatories.index', compact('formSignatories', 'forms', 'departments', 'users'));
    }

    public function store(FormSignatoryRequest $request)
    {
        $validated = $request->validated();

        // Check if assigning to all departments
        if ($request->has('assign_to_all_departments') && $request->assign_to_all_departments) {
            $result = $this->service->assignSignatoriesToAllDepartments(
                $validated['form_id'],
                $validated['signatories']
            );

            if ($request->wantsJson()) {
                return response()->json(['data' => $result['signatories']], 201);
            }

            return redirect()->back()->with('success', "Form signatories assigned successfully to all {$result['department_count']} departments!");
        }

        // Assign to specific department
        $formSignatories = $this->service->assignSignatories(
            $validated['form_id'],
            $validated['department_id'],
            $validated['signatories']
        );

        if ($request->wantsJson()) {
            return response()->json(['data' => $formSignatories], 201);
        }

        return redirect()->back()->with('success', 'Form signatories assigned successfully!');
    }

    public function edit($id)
    {
        $formSignatory = $this->service->findSignatory($id);
        $formSignatories = $this->service->getAllSignatoriesGrouped();
        $forms = $this->service->getAllForms();
        $departments = $this->service->getAllDepartments();
        $users = $this->service->getAllUsers();

        // Get all signatories for this form and department
        $currentSignatories = $this->service->getSignatoryIdsByFormAndDepartment(
            $formSignatory->form_id,
            $formSignatory->department_id
        );

        return view('form-signatories.index', compact('formSignatories', 'forms', 'departments', 'users', 'formSignatory', 'currentSignatories'));
    }

    public function update(FormSignatoryRequest $request, $id)
    {
        $formSignatory = $this->service->findSignatory($id);
        $validated = $request->validated();

        // Check if assigning to all departments
        if ($request->has('assign_to_all_departments') && $request->assign_to_all_departments) {
            $result = $this->service->assignSignatoriesToAllDepartments(
                $validated['form_id'],
                $validated['signatories']
            );

            if ($request->wantsJson()) {
                return response()->json(['data' => $result['signatories']]);
            }

            return redirect()->route('form-signatories.index')->with('success', "Form signatories assigned successfully to all {$result['department_count']} departments!");
        }

        // Update specific department
        $formSignatories = $this->service->assignSignatories(
            $validated['form_id'],
            $validated['department_id'],
            $validated['signatories']
        );

        if ($request->wantsJson()) {
            return response()->json(['data' => $formSignatories]);
        }

        return redirect()->route('form-signatories.index')->with('success', 'Form signatories updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $formSignatory = $this->service->findSignatory($id);
        
        // Delete all signatories for this form and department
        $this->service->deleteSignatoriesByFormAndDepartment(
            $formSignatory->form_id,
            $formSignatory->department_id
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Form signatories deleted successfully']);
        }

        return redirect()->route('form-signatories.index')->with('success', 'Form signatories deleted successfully!');
    }
}
