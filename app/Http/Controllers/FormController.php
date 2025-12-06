<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Http\Requests\FormRequest;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $forms = Form::orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $forms]);
        }

        return view('forms.index', compact('forms'));
    }

    public function store(FormRequest $request)
    {

        $form = Form::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        if ($request->wantsJson()) {
            return response()->json(['data' => $form], 201);
        }

        return redirect()->back()->with('success', 'Form created successfully!');
    }

    public function edit($id)
    {
        $form = Form::findOrFail($id);
        $forms = Form::orderBy('name')->get();

        return view('forms.index', compact('forms', 'form'));
    }

    public function update(FormRequest $request, $id)
    {
        $form = Form::findOrFail($id);

        $form->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', $form->is_active)
        ]);

        if ($request->wantsJson()) {
            return response()->json(['data' => $form]);
        }

        return redirect()->route('forms.index')->with('success', 'Form updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        
        // Check if form is being used by form signatories
        if ($form->formSignatories()->count() > 0) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Cannot delete form that is being used by form signatories'], 422);
            }
            return redirect()->back()->with('error', 'Cannot delete form that is being used by form signatories');
        }

        $form->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Form deleted successfully']);
        }

        return redirect()->route('forms.index')->with('success', 'Form deleted successfully!');
    }
}