<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fiscal_year_id' => ['required', 'exists:fiscal_years,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'effective_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'total_steps' => ['required', 'integer', 'min:1', 'max:12'],
            'max_grade' => ['required', 'integer', 'min:1', 'max:99'],
            'cells' => ['required', 'array'],
            'cells.*.*' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
