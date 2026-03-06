<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiscalYearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fiscalYearId = $this->route('fiscal_year') ? $this->route('fiscal_year')->id : null;
        
        return [
            'year' => 'required|integer|min:2020|max:2050|unique:fiscal_years,year,' . $fiscalYearId,
            'description' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_current' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'year.required' => 'The fiscal year is required.',
            'year.unique' => 'This fiscal year already exists.',
            'year.min' => 'The fiscal year must be at least 2020.',
            'year.max' => 'The fiscal year cannot be greater than 2050.',
            'start_date.required' => 'The start date is required.',
            'end_date.required' => 'The end date is required.',
            'end_date.after' => 'The end date must be after the start date.',
        ];
    }
}