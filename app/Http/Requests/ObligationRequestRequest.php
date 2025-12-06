<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObligationRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $obrId = $this->route('obligation_request');
        
        return [
            'obr_number' => 'required|string|max:255|unique:obligation_requests,obr_number,' . $obrId,
            'department_id' => 'required|exists:departments,id',
            'claimant_payee_id' => 'required|exists:claimant_payees,id',
            'obligation_date' => 'required|date',
            'particulars' => 'required|string',
            'optional_field_1' => 'nullable|string',
            'optional_field_2' => 'nullable|string',
            'signatory_1_id' => 'nullable|exists:users,id',
            'signatory_1_date' => 'nullable|date',
            'signatory_2_id' => 'nullable|exists:users,id',
            'signatory_2_date' => 'nullable|date',
            'noted_signatory_id' => 'nullable|exists:users,id',
            'noted_signatory_date' => 'nullable|date',
            'fund_type_id' => 'nullable|exists:fund_types,id',
            'budget_year' => 'nullable|integer|min:2020|max:' . (date('Y') + 5),
            'items' => 'required|array|min:1',
            'items.*.department_id' => 'required|exists:departments,id',
            'items.*.expense_type_id' => 'required|exists:expense_types,id',
            'items.*.account_id' => 'nullable|exists:accounts,id',
            'items.*.sub_account_id' => 'nullable|exists:sub_accounts,id',
            'items.*.amount' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'obr_number.required' => 'OBR number is required.',
            'obr_number.unique' => 'This OBR number already exists.',
            'department_id.required' => 'Please select a responsibility center.',
            'claimant_payee_id.required' => 'Please select a claimant payee.',
            'obligation_date.required' => 'Obligation date is required.',
            'particulars.required' => 'Particulars are required.',
            'signatory_1_id.required' => 'First signatory is required.',
            'signatory_2_id.required' => 'Second signatory is required.',
            'noted_signatory_id.required' => 'Noted signatory is required.',
            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
        ];
    }
}
