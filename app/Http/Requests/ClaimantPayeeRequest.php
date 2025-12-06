<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimantPayeeRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'department_id' => 'required|exists:departments,id',
            'payee_category_id' => 'required|exists:payee_categories,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The payee name is required.',
            'name.max' => 'The payee name may not be greater than 255 characters.',
            'address.required' => 'The address is required.',
            'address.max' => 'The address may not be greater than 1000 characters.',
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'The selected department does not exist.',
            'payee_category_id.required' => 'Please select a category.',
            'payee_category_id.exists' => 'The selected category does not exist.',
        ];
    }
}
