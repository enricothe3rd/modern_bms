<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentExpenseTypeAllocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean the amount by removing commas before validation
        $this->merge([
            'amount' => str_replace(',', '', $this->input('amount', ''))
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'account_type' => 'required|in:account,sub_account',
            'account_id' => 'required_if:account_type,account|nullable|exists:accounts,id',
            'sub_account_id' => 'required_if:account_type,sub_account|nullable|exists:sub_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'fiscal_year_id.required' => 'Please select a fiscal year.',
            'fiscal_year_id.exists' => 'The selected fiscal year is invalid.',
            'account_type.required' => 'Please select an account type.',
            'account_id.required_if' => 'Please select an account.',
            'sub_account_id.required_if' => 'Please select a sub-account.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a valid number.',
            'amount.min' => 'The amount must be at least 0.',
        ];
    }
}
