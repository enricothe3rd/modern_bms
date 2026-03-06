<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatementOfStatutoryObligationRequest extends FormRequest
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
            'remarks' => ['nullable', 'string'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*.category_number' => ['required', 'string', 'max:50'],
            'categories.*.category_name' => ['required', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*.category_ref' => ['required', 'integer', 'min:0'],
            'items.*.code' => ['nullable', 'string', 'max:100'],
            'items.*.description' => ['required', 'string'],
            'items.*.amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
