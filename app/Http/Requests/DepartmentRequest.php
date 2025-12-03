<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
{
    // Allow all users (or implement your auth logic)
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get department ID for update
        $id = $this->route('department'); // automatically gets the {department} route param

        return [
            'code' => 'required|string|unique:departments,code' . ($id ? ',' . $id : ''),
            'name' => 'required|string',
            'sector_name' => 'required|string',
        ];
    }
}
