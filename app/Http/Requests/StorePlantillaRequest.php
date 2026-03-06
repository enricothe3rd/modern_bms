<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlantillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fiscal_year_id' => ['nullable', 'exists:fiscal_years,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'group_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.employee_name' => ['required', 'string', 'max:255'],
            'items.*.position' => ['required', 'string', 'max:255'],
            'items.*.old_count' => ['required', 'integer', 'min:0'],
            'items.*.new_count' => ['required', 'integer', 'min:0'],
            'items.*.to_groups' => ['required', 'array', 'min:1'],
            'items.*.to_groups.*.group_label' => ['nullable', 'string', 'max:255'],
            'items.*.to_groups.*.movements' => ['required', 'array', 'min:1'],
            'items.*.to_groups.*.movements.*.label' => ['nullable', 'string', 'max:255'],
            'items.*.to_groups.*.movements.*.salary_schedule_id' => ['nullable', 'exists:salary_schedules,id'],
            'items.*.to_groups.*.movements.*.salary_cell' => ['nullable', 'string', 'regex:/^\d+-\d+$/'],
            'items.*.from_groups' => ['required', 'array', 'min:1'],
            'items.*.from_groups.*.group_label' => ['nullable', 'string', 'max:255'],
            'items.*.from_groups.*.movements' => ['required', 'array', 'min:1'],
            'items.*.from_groups.*.movements.*.label' => ['nullable', 'string', 'max:255'],
            'items.*.from_groups.*.movements.*.salary_schedule_id' => ['nullable', 'exists:salary_schedules,id'],
            'items.*.from_groups.*.movements.*.salary_cell' => ['nullable', 'string', 'regex:/^\d+-\d+$/'],
        ];
    }
}
