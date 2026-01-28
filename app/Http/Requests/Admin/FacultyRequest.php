<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FacultyRequest extends FormRequest
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
        $facultyId = $this->faculty ? $this->faculty->id : null;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:faculties,code,' . $facultyId,
        ];
    }
}
