<?php

namespace App\Http\Requests;

use App\Models\FiscalYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRealignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'description' => 'required|string|max:1000',
            'fiscal_year_id' => [
                'required',
                'integer',
                Rule::exists('fiscal_years', 'id')->where('is_active', true)
            ],
            'from_items' => 'required|array|min:1',
            'from_items.*.department_id' => 'required|exists:departments,id',
            'from_items.*.expense_type_id' => 'required|exists:expense_types,id',
            'from_items.*.account_id' => 'required|exists:accounts,id',
            'from_items.*.sub_account_id' => 'nullable|exists:sub_accounts,id',
            'from_items.*.amount' => 'required|numeric|min:0.01',
            'from_items.*.remarks' => 'nullable|string|max:500',
            'to_items' => 'required|array|min:1',
            'to_items.*.department_id' => 'required|exists:departments,id',
            'to_items.*.expense_type_id' => 'required|exists:expense_types,id',
            'to_items.*.account_id' => 'required|exists:accounts,id',
            'to_items.*.sub_account_id' => 'nullable|exists:sub_accounts,id',
            'to_items.*.amount' => 'required|numeric|min:0.01',
            'to_items.*.remarks' => 'nullable|string|max:500',
        ];

        // For updates, we don't need fiscal_year_id as it shouldn't change
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($rules['fiscal_year_id']);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'description.required' => 'The description field is required.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'fiscal_year_id.required' => 'Please select a fiscal year.',
            'fiscal_year_id.exists' => 'The selected fiscal year is invalid or inactive.',
            'from_items.required' => 'At least one source item is required.',
            'from_items.min' => 'At least one source item is required.',
            'from_items.*.department_id.required' => 'Department is required for each source item.',
            'from_items.*.department_id.exists' => 'The selected department is invalid.',
            'from_items.*.expense_type_id.required' => 'Expense type is required for each source item.',
            'from_items.*.expense_type_id.exists' => 'The selected expense type is invalid.',
            'from_items.*.account_id.required' => 'Account is required for each source item.',
            'from_items.*.account_id.exists' => 'The selected account is invalid.',
            'from_items.*.sub_account_id.exists' => 'The selected sub account is invalid.',
            'from_items.*.amount.required' => 'Amount is required for each source item.',
            'from_items.*.amount.numeric' => 'Amount must be a valid number.',
            'from_items.*.amount.min' => 'Amount must be at least 0.01.',
            'from_items.*.remarks.max' => 'Remarks may not be greater than 500 characters.',
            'to_items.required' => 'At least one destination item is required.',
            'to_items.min' => 'At least one destination item is required.',
            'to_items.*.department_id.required' => 'Department is required for each destination item.',
            'to_items.*.department_id.exists' => 'The selected department is invalid.',
            'to_items.*.expense_type_id.required' => 'Expense type is required for each destination item.',
            'to_items.*.expense_type_id.exists' => 'The selected expense type is invalid.',
            'to_items.*.account_id.required' => 'Account is required for each destination item.',
            'to_items.*.account_id.exists' => 'The selected account is invalid.',
            'to_items.*.sub_account_id.exists' => 'The selected sub account is invalid.',
            'to_items.*.amount.required' => 'Amount is required for each destination item.',
            'to_items.*.amount.numeric' => 'Amount must be a valid number.',
            'to_items.*.amount.min' => 'Amount must be at least 0.01.',
            'to_items.*.remarks.max' => 'Remarks may not be greater than 500 characters.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that total from amounts equal total to amounts
            $totalFromAmount = collect($this->input('from_items', []))->sum('amount');
            $totalToAmount = collect($this->input('to_items', []))->sum('amount');

            if (abs($totalFromAmount - $totalToAmount) > 0.01) {
                $validator->errors()->add('total_amount', 'Total "From" amount must equal total "To" amount.');
            }

            // Validate no duplicate combinations in from items
            $fromCombinations = collect($this->input('from_items', []))->map(function($item) {
                return $item['department_id'] . '-' . $item['expense_type_id'] . '-' . $item['account_id'] . '-' . ($item['sub_account_id'] ?? 'null');
            });
            if ($fromCombinations->count() !== $fromCombinations->unique()->count()) {
                $validator->errors()->add('from_items', 'Duplicate source allocations are not allowed.');
            }

            // Validate no duplicate combinations in to items
            $toCombinations = collect($this->input('to_items', []))->map(function($item) {
                return $item['department_id'] . '-' . $item['expense_type_id'] . '-' . $item['account_id'] . '-' . ($item['sub_account_id'] ?? 'null');
            });
            if ($toCombinations->count() !== $toCombinations->unique()->count()) {
                $validator->errors()->add('to_items', 'Duplicate destination allocations are not allowed.');
            }

            // Validate that a combination is not used in both from and to items
            $commonCombinations = $fromCombinations->intersect($toCombinations);
            if ($commonCombinations->isNotEmpty()) {
                $validator->errors()->add('allocations', 'An allocation cannot be both a source and destination in the same realignment.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        // Clean up empty items
        $fromItems = collect($this->input('from_items', []))
            ->filter(function ($item) {
                return !empty($item['allocation_id']) || !empty($item['amount']);
            })
            ->values()
            ->toArray();

        $toItems = collect($this->input('to_items', []))
            ->filter(function ($item) {
                return !empty($item['allocation_id']) || !empty($item['amount']);
            })
            ->values()
            ->toArray();

        $this->merge([
            'from_items' => $fromItems,
            'to_items' => $toItems,
        ]);
    }
}