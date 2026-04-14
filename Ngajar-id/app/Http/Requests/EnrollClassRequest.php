<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $class_id
 */
class EnrollClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'class_id' => ['required', 'integer', 'exists:kelas,kelas_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Class ID is required',
            'class_id.exists' => 'Class not found',
        ];
    }
}
