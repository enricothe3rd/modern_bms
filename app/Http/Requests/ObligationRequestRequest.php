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

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateBudgetAvailability($validator);
        });
    }

    /**
     * Validate that each item's amount doesn't exceed available budget
     */
    protected function validateBudgetAvailability($validator)
    {
        if (!$this->has('items') || !is_array($this->items)) {
            return;
        }

        $obligationDate = $this->obligation_date;
        $year = date('Y', strtotime($obligationDate));
        $obrId = $this->route('obligation_request'); // For updates, exclude current OBR

        foreach ($this->items as $index => $item) {
            if (!isset($item['department_id']) || !isset($item['expense_type_id'])) {
                continue;
            }

            $departmentId = $item['department_id'];
            $expenseTypeId = $item['expense_type_id'];
            $accountId = $item['account_id'] ?? null;
            $subAccountId = $item['sub_account_id'] ?? null;
            $requestedAmount = $item['amount'] ?? 0;

            // Get allocated amount
            $allocation = \App\Models\DepartmentExpenseTypeAllocation::where('department_id', $departmentId)
                ->where('expense_type_id', $expenseTypeId)
                ->where('year', $year);

            if ($subAccountId) {
                $allocation->where('sub_account_id', $subAccountId);
            } elseif ($accountId) {
                $allocation->where('account_id', $accountId)
                    ->whereNull('sub_account_id');
            } else {
                continue; // Skip if no account specified
            }

            $allocatedAmount = $allocation->sum('amount');

            if ($allocatedAmount == 0) {
                $validator->errors()->add(
                    "items.{$index}.amount",
                    "No budget allocation found for this account in year {$year}."
                );
                continue;
            }

            // Calculate already obligated amount (excluding current OBR if updating)
            $obligatedQuery = \App\Models\ObligationRequestItem::whereHas('obligationRequest', function($query) use ($year) {
                    $query->whereYear('obligation_date', $year);
                })
                ->where('department_id', $departmentId)
                ->where('expense_type_id', $expenseTypeId);

            if ($subAccountId) {
                $obligatedQuery->where('sub_account_id', $subAccountId);
            } elseif ($accountId) {
                $obligatedQuery->where('account_id', $accountId)
                    ->whereNull('sub_account_id');
            }

            // Exclude current OBR items if updating
            if ($obrId) {
                $obligatedQuery->where('obligation_request_id', '!=', $obrId);
            }

            $obligatedAmount = $obligatedQuery->sum('amount');
            $availableAmount = $allocatedAmount - $obligatedAmount;

            // Check if requested amount exceeds available
            if ($requestedAmount > $availableAmount) {
                $accountInfo = $subAccountId 
                    ? \App\Models\SubAccount::find($subAccountId)?->code 
                    : \App\Models\Account::find($accountId)?->code;
                
                $validator->errors()->add(
                    "items.{$index}.amount",
                    "Amount ₱" . number_format($requestedAmount, 2) . " exceeds available budget of ₱" . number_format($availableAmount, 2) . " for account {$accountInfo}. Another user may have created an obligation. Please refresh and try again."
                );
            }
        }
    }
}
