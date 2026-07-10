<?php

namespace App\Imports;

use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $defaultRole;

    public function __construct($defaultRole = null)
    {
        $this->defaultRole = $defaultRole;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Find program studi by name
        $prodi = null;
        if (!empty($row['program_studi'])) {
            $prodi = ProgramStudi::where('name', 'like', '%' . $row['program_studi'] . '%')->first();
        }

        // Avoid duplicates by email or NIM/NIP
        $nimNip = $row['nim_nip'] ?? $row['nimnip'] ?? $row['nim'] ?? $row['nip'] ?? null;

        $user = User::where('email', $row['email'])
            ->orWhere(function($q) use ($nimNip) {
                if (!empty($nimNip)) {
                    $q->where('nim_nip', $nimNip);
                }
            })->first();

        if ($user) {
            // Update existing user
            $user->update([
                'name' => $row['nama'] ?? $user->name,
                'nim_nip' => $nimNip ?? $user->nim_nip,
                'phone' => $row['telepon'] ?? $user->phone,
                'address' => $row['alamat'] ?? $user->address,
                'program_studi_id' => $prodi ? $prodi->id : $user->program_studi_id,
                'angkatan' => $row['angkatan'] ?? $user->angkatan,
            ]);
            
            // Update password only if provided
            if (!empty($row['password'])) {
                $user->update(['password' => Hash::make($row['password'])]);
            }
        } else {
            // Create new user
            $user = User::create([
                'name' => $row['nama'],
                'email' => $row['email'],
                'nim_nip' => $nimNip,
                'password' => Hash::make($row['password'] ?? 'password123'),
                'phone' => $row['telepon'] ?? null,
                'address' => $row['alamat'] ?? null,
                'program_studi_id' => $prodi ? $prodi->id : null,
                'angkatan' => $row['angkatan'] ?? null,
                'is_active' => true,
            ]);
        }

        // Assign role: Kaprodi uses defaultRole, Admin uses Excel column
        $roleName = $this->defaultRole ?: ($row['role'] ?? null);
        
        if ($roleName) {
            $roleName = strtolower(trim($roleName));
            if (Role::where('name', $roleName)->exists()) {
                $rolesToSync = [$roleName];
                if ($user->exists && $user->hasRole('kaprodi')) {
                    $rolesToSync[] = 'kaprodi';
                }
                $user->syncRoles(array_unique($rolesToSync));
            }
        }

        return $user;
    }

    public function rules(): array
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'nim_nip' => 'nullable',
            'nimnip' => 'nullable',
            'nim' => 'nullable',
            'nip' => 'nullable',
            'program_studi' => 'nullable|string',
            'angkatan' => 'nullable|numeric',
        ];

        // If no default role, role column is required and must be valid
        if (!$this->defaultRole) {
            $validRoles = Role::pluck('name')->toArray();
            $rules['role'] = ['required', Rule::in($validRoles)];
        }

        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'role.in' => 'Role ":input" tidak valid. Pilih dari: ' . Role::pluck('name')->implode(', '),
            'email.required' => 'Email wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
        ];
    }
}
