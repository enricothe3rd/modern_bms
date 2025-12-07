<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserDepartmentAssignmentRequest extends FormRequest
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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        $rules = [
            'user_id' => 'required|exists:users,id',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
            'review_status_ids' => 'nullable|array',
            'review_status_ids.*' => 'exists:review_statuses,id'
        ];

        if ($isUpdate) {
            // For update, support both single and multiple departments
            if ($this->has('department_ids')) {
                $rules['department_ids'] = 'required|array|min:1|max:1'; // Only one for update
                $rules['department_ids.*'] = 'exists:departments,id';
            } else {
                $rules['department_id'] = 'required|exists:departments,id';
            }
        } else {
            // For store, allow multiple departments
            $rules['department_ids'] = 'required|array|min:1';
            $rules['department_ids.*'] = 'exists:departments,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user does not exist.',
            'department_ids.required' => 'Please select at least one department.',
            'department_ids.min' => 'Please select at least one department.',
            'department_ids.max' => 'Please select exactly one department when editing.',
            'department_ids.*.exists' => 'One or more selected departments do not exist.',
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'The selected department does not exist.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'notes.max' => 'Notes may not be greater than 1000 characters.',
            'review_status_ids.array' => 'Review statuses must be an array.',
            'review_status_ids.*.exists' => 'One or more selected review statuses do not exist.',
        ];
    }
}
