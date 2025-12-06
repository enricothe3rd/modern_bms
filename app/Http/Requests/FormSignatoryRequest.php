<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormSignatoryRequest extends FormRequest
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
        $rules = [
            'form_id' => 'required|exists:forms,id',
            'signatories' => 'required|array|min:1',
            'signatories.*' => 'exists:users,id'
        ];

        // Department is required unless assigning to all departments
        if (!$this->has('assign_to_all_departments') || !$this->assign_to_all_departments) {
            $rules['department_id'] = 'required|exists:departments,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'form_id.required' => 'Please select a form.',
            'form_id.exists' => 'The selected form does not exist.',
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'The selected department does not exist.',
            'signatories.required' => 'Please select at least one signatory.',
            'signatories.min' => 'Please select at least one signatory.',
            'signatories.*.exists' => 'One or more selected signatories do not exist.',
        ];
    }
}
