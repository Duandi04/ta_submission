<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProgramStudiRequest extends FormRequest
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
        $programStudiId = $this->program_studi ? $this->program_studi->id : null;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:program_studis,code,' . $programStudiId,
            'faculty_id' => 'required|exists:faculties,id',
            'submission_start' => 'nullable|date',
            'submission_end' => 'nullable|date|after_or_equal:submission_start',
        ];
    }
}
