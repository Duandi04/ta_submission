<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $userParam = $this->route('user') ?: $this->route('student') ?: $this->route('lecturer') ?: $this->route('manage');
        $userId = $userParam instanceof \App\Models\User ? $userParam->id : $userParam;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'password' => $this->isMethod('POST') ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'nim_nip' => 'nullable|string|max:50|unique:users,nim_nip,' . $userId,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|string|exists:roles,name',
            'program_studi_id' => 'required_if:role,mahasiswa,dosen,kaprodi|nullable|exists:program_studis,id',
            'is_active' => 'boolean',
            'angkatan' => 'nullable|integer|min:2000|max:2099',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
            'can_exceed_submission_limit' => 'nullable|boolean',
        ];

        return $rules;
    }
}
