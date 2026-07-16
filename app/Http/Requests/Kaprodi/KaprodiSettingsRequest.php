<?php

namespace App\Http\Requests\Kaprodi;

use Illuminate\Foundation\Http\FormRequest;

class KaprodiSettingsRequest extends FormRequest
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
            'max_batches' => 'required|integer|min:1',
            'attempts_per_batch' => 'required|integer|min:1',
            'submission_start' => 'nullable|date',
            'submission_end' => 'nullable|date|after_or_equal:submission_start',
        ];
    }
}
