<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
            'password' => $this->isMethod('POST')
                ? ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()]
                : ['nullable', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'nim_nip' => 'nullable|string|max:50|unique:users,nim_nip,' . $userId,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            // Support both single role (role) and array of roles (roles)
            'role' => 'required_without:roles|string|exists:roles,name',
            'roles' => 'required_without:role|array',
            'roles.*' => 'exists:roles,name',
            
            // Direct permissions
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',

            'program_studi_id' => [
                'nullable',
                'exists:program_studis,id',
                function ($attribute, $value, $fail) {
                    $role = $this->input('role');
                    $roles = $this->input('roles', $role ? [$role] : []);
                    $requiredRoles = ['mahasiswa', 'dosen', 'kaprodi'];
                    $hasRequiredRole = count(array_intersect($roles, $requiredRoles)) > 0;
                    if ($hasRequiredRole && empty($value)) {
                        $fail('Program Studi wajib diisi untuk mahasiswa, dosen, atau kaprodi.');
                    }
                }
            ],
            'is_active' => 'boolean',
            'angkatan' => 'nullable|integer|min:2000|max:2099',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
            'can_exceed_submission_limit' => 'nullable|boolean',
        ];

        return $rules;
    }
}
