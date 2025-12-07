<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('review_status');
        
        return [
            'name' => 'required|string|max:255|unique:review_statuses,name,' . $id,
            'code' => 'nullable|string|max:255|unique:review_statuses,code,' . $id,
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7', // Hex color
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Status name is required.',
            'name.unique' => 'This status name already exists.',
            'code.unique' => 'This status code already exists.',
            'color.required' => 'Please select a color.',
            'order.required' => 'Order is required.',
            'order.integer' => 'Order must be a number.',
        ];
    }
}