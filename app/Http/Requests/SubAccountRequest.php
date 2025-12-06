<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubAccountRequest extends FormRequest
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
        $subAccountId = $this->route('sub_account');
        
        return [
            'code' => 'required|string|max:255|unique:sub_accounts,code,' . $subAccountId,
            'description' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'The sub-account code is required.',
            'code.unique' => 'This sub-account code already exists.',
            'description.required' => 'The sub-account description is required.',
            'account_id.required' => 'Please select a parent account.',
            'account_id.exists' => 'The selected parent account does not exist.'
        ];
    }
}
