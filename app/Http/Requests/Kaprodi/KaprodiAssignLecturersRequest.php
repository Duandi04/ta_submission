<?php

namespace App\Http\Requests\Kaprodi;

use Illuminate\Foundation\Http\FormRequest;

class KaprodiAssignLecturersRequest extends FormRequest
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
        return [
            'assessor_ids' => 'required|array|min:1',
            'assessor_ids.*' => 'exists:users,id',
            'rubric_id' => 'required|exists:rubrics,id',
            'supervisor_id' => 'required|exists:users,id',
            'supervisor_2_id' => 'nullable|exists:users,id|different:supervisor_id',
        ];
    }
}
