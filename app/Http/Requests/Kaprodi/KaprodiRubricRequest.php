<?php

namespace App\Http\Requests\Kaprodi;

use Illuminate\Foundation\Http\FormRequest;

class KaprodiRubricRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.weight' => 'required|numeric|min:0|max:100',
            'criteria.*.description' => 'nullable|string',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $totalWeight = array_sum(array_column($this->criteria ?? [], 'weight'));
            if ($totalWeight != 100) {
                $validator->errors()->add('criteria', 'Total bobot kriteria harus tepat 100% (saat ini ' . $totalWeight . '%).');
            }
        });
    }
}
