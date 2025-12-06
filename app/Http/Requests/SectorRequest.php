<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectorRequest extends FormRequest
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
        $id = $this->route('sector');
        
        return [
            'name' => 'required|string|max:255|unique:sectors,name' . ($id ? ',' . $id : ''),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The sector name is required.',
            'name.unique' => 'A sector with this name already exists.',
            'name.max' => 'The sector name may not be greater than 255 characters.',
        ];
    }
}