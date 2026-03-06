<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatementOfIndebtednessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fiscal_year_id' => ['required', 'exists:fiscal_years,id'],
            'creditor' => ['required', 'string', 'max:255'],
            'date_contracted' => ['required', 'date'],
            'term_maturity' => ['required', 'string', 'max:255'],
            'principal_amount' => ['required', 'numeric', 'min:0'],
            'purpose' => ['required', 'string'],
            'prev_principal' => ['nullable', 'numeric', 'min:0'],
            'prev_interest' => ['nullable', 'numeric', 'min:0'],
            'prev_total' => ['nullable', 'numeric', 'min:0'],
            'due_principal' => ['required', 'numeric', 'min:0'],
            'due_interest' => ['required', 'numeric', 'min:0'],
            'due_total' => ['nullable', 'numeric', 'min:0'],
            'balance' => ['nullable', 'numeric'],
        ];
    }
}
