<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Log the incoming request data before validation
        Log::info('DepartmentRequest data received', $this->all());
    }

    public function rules(): array
    {
        $id = $this->route('department'); // automatically gets {department} route param

        return [
            'code' => 'required|string|unique:departments,code' . ($id ? ',' . $id : ''),
            'name' => 'required|string',
            'sector_id' => 'required|exists:sectors,id',
        ];
    }
}
