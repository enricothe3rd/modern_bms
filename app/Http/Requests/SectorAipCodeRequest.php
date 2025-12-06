<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectorAipCodeRequest extends FormRequest
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
        $aipCodeId = $this->route('aipCode');
        
        return [
            'code' => 'required|string|max:50|unique:sector_aip_codes,code' . ($aipCodeId ? ',' . $aipCodeId : ''),
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'The AIP code is required.',
            'code.unique' => 'This AIP code already exists.',
            'code.max' => 'The AIP code may not be greater than 50 characters.',
            'description.max' => 'The description may not be greater than 255 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active')
        ]);
    }
}
