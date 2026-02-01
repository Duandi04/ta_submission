<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Core Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@ta.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'nim_nip' => 'ADM001',
                'phone' => '081122334455',
                'profile_photo' => 'profile-photos/admin.png',
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        // Core Koordinator User
        $koordinator = User::updateOrCreate(
            ['email' => 'koordinator@ta.test'],
            [
                'name' => 'Dr. Budi Santoso (Koordinator)',
                'password' => Hash::make('password'),
                'nim_nip' => 'KOR001',
                'phone' => '081122334466',
                'profile_photo' => 'profile-photos/lecturer.png',
                'is_active' => true,
            ]
        );
        $koordinator->syncRoles(['koordinator']);

        // Balanced users for each Program Studi
        $programStudis = ProgramStudi::all();

        foreach ($programStudis as $prodi) {
            $prodiCode = strtolower($prodi->code);

            // 1. Kaprodi per Prodi
            $kaprodi = User::updateOrCreate(
                ['email' => "kaprodi_{$prodiCode}@ta.test"],
                [
                    'name' => "Kaprodi {$prodi->name}",
                    'password' => Hash::make('password'),
                    'nim_nip' => 'KPD' . strtoupper($prodiCode) . '001',
                    'phone' => '081' . rand(100000000, 999999999),
                    'profile_photo' => 'profile-photos/lecturer.png',
                    'is_active' => true,
                    'program_studi_id' => $prodi->id,
                ]
            );
            $kaprodi->syncRoles(['kaprodi', 'dosen']);

            // 2. 3 Dosen per Prodi
            for ($i = 1; $i <= 3; $i++) {
                $dosen = User::updateOrCreate(
                    ['email' => "dosen_{$prodiCode}_{$i}@ta.test"],
                    [
                        'name' => "Dosen {$prodi->code} {$i}",
                        'password' => Hash::make('password'),
                        'nim_nip' => 'DSN' . strtoupper($prodiCode) . '00' . $i,
                        'phone' => '082' . rand(100000000, 999999999),
                        'profile_photo' => 'profile-photos/lecturer.png',
                        'is_active' => true,
                        'program_studi_id' => $prodi->id,
                    ]
                );
                $dosen->syncRoles(['dosen']);
            }

            // 3. 5 Mahasiswa per Prodi
            for ($i = 1; $i <= 5; $i++) {
                $mahasiswa = User::updateOrCreate(
                    ['email' => "mhs_{$prodiCode}_{$i}@ta.test"],
                    [
                        'name' => "Mahasiswa {$prodi->code} {$i}",
                        'password' => Hash::make('password'),
                        'nim_nip' => date('Y') . $prodi->id . str_pad($i, 4, '0', STR_PAD_LEFT),
                        'phone' => '085' . rand(100000000, 999999999),
                        'address' => "Alamat Mahasiswa {$i} Prodi {$prodi->name}",
                        'profile_photo' => 'profile-photos/student.png',
                        'is_active' => true,
                        'program_studi_id' => $prodi->id,
                    ]
                );
                $mahasiswa->syncRoles(['mahasiswa']);
            }
        }
    }
}
