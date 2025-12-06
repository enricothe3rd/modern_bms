<?php

namespace App\Repositories;

use App\Models\FormSignatory;

class FormSignatoryRepository
{
    public function allGrouped()
    {
        return FormSignatory::with(['form', 'department', 'signatory'])
            ->orderBy('form_id')
            ->orderBy('department_id')
            ->orderBy('order')
            ->get()
            ->groupBy(function($item) {
                return $item->form ? $item->form->name : $item->form_name;
            })
            ->map(function($group) {
                return $group->groupBy('department_id');
            });
    }

    public function find($id)
    {
        return FormSignatory::findOrFail($id);
    }

    public function create(array $data)
    {
        return FormSignatory::create($data);
    }

    public function deleteByFormAndDepartment($formId, $departmentId)
    {
        return FormSignatory::where('form_id', $formId)
            ->where('department_id', $departmentId)
            ->delete();
    }

    public function deleteByForm($formId)
    {
        return FormSignatory::where('form_id', $formId)->delete();
    }

    public function getByFormAndDepartment($formId, $departmentId)
    {
        return FormSignatory::with(['form', 'department', 'signatory'])
            ->where('form_id', $formId)
            ->where('department_id', $departmentId)
            ->orderBy('order')
            ->get();
    }

    public function getByForm($formId)
    {
        return FormSignatory::with(['form', 'department', 'signatory'])
            ->where('form_id', $formId)
            ->orderBy('department_id')
            ->orderBy('order')
            ->get();
    }

    public function getSignatoryIdsByFormAndDepartment($formId, $departmentId)
    {
        return FormSignatory::where('form_id', $formId)
            ->where('department_id', $departmentId)
            ->orderBy('order')
            ->pluck('signatory_id')
            ->toArray();
    }
}
