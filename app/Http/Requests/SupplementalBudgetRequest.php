<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplementalBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'supplemental_group' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.department_id' => 'required|exists:departments,id',
            'items.*.expense_type_id' => 'required|exists:expense_types,id',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.sub_account_id' => 'nullable|exists:sub_accounts,id',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.justification' => 'nullable|string',
            'items.*.remarks' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'fiscal_year_id.required' => 'The fiscal year field is required.',
            'fiscal_year_id.exists' => 'The selected fiscal year is invalid.',
            'supplemental_group.required' => 'The supplemental group field is required.',
            'items.required' => 'At least one budget item is required.',
            'items.min' => 'At least one budget item is required.',
            'items.*.department_id.required' => 'Department is required for each item.',
            'items.*.department_id.exists' => 'Selected department is invalid.',
            'items.*.expense_type_id.required' => 'Expense type is required for each item.',
            'items.*.expense_type_id.exists' => 'Selected expense type is invalid.',
            'items.*.account_id.required' => 'Account is required for each item.',
            'items.*.account_id.exists' => 'Selected account is invalid.',
            'items.*.sub_account_id.exists' => 'Selected sub account is invalid.',
            'items.*.amount.required' => 'Amount is required for each item.',
            'items.*.amount.numeric' => 'Amount must be a valid number.',
            'items.*.amount.min' => 'Amount must be greater than 0.',
        ];
    }
}