<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
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
        $formId = $this->route('form');
        
        return [
            'name' => 'required|string|max:255|unique:forms,name,' . $formId,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The form name is required.',
            'name.unique' => 'A form with this name already exists.',
            'name.max' => 'The form name may not be greater than 255 characters.',
            'description.max' => 'The description may not be greater than 500 characters.',
        ];
    }
}