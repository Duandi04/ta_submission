<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    protected $role;
    protected $programStudiId;
    protected $search;

    public function __construct($role = null, $programStudiId = null, $search = null)
    {
        $this->role = $role;
        $this->programStudiId = $programStudiId;
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with(['roles', 'programStudi'])
            ->when($this->role, function ($q) {
                $q->role($this->role);
            })
            ->when($this->programStudiId, function ($q) {
                $q->where('program_studi_id', $this->programStudiId);
            })
            ->when($this->search, function ($q) {
                $q->search($this->search);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'NIM/NIP',
            'Password',
            'Telepon',
            'Alamat',
            'Program Studi',
            'Angkatan',
            'Role',
            'Status',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->nim_nip,
            '', // Password column left empty for import
            $user->phone,
            $user->address,
            $user->programStudi ? $user->programStudi->name : '-',
            $user->angkatan,
            $user->roles->pluck('name')->implode(', '),
            $user->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }
}
