<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseTypeRequest extends FormRequest
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
        $expenseTypeId = $this->route('expense_type');
        
        return [
            'description' => 'required|string|max:255',
            'acronym' => 'required|string|max:10|unique:expense_types,acronym,' . $expenseTypeId
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'description.required' => 'The expense type description is required.',
            'acronym.required' => 'The acronym is required.',
            'acronym.unique' => 'This acronym already exists.',
            'acronym.max' => 'The acronym must not exceed 10 characters.'
        ];
    }
}
