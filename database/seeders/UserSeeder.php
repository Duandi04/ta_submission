<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => 'ADM001',
            'phone' => '081234567890',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Koordinator
        $koordinator = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'koordinator@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => '197001011995011001',
            'phone' => '081234567891',
            'is_active' => true,
        ]);
        $koordinator->assignRole('koordinator');

        // Dosen Pembimbing
        $pembimbing1 = User::create([
            'name' => 'Dr. Siti Aminah',
            'email' => 'pembimbing1@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => '197505151998022001',
            'phone' => '081234567892',
            'is_active' => true,
        ]);
        $pembimbing1->assignRole('dosen_pembimbing');

        $pembimbing2 = User::create([
            'name' => 'Prof. Ahmad Rahman',
            'email' => 'pembimbing2@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => '196803101992031001',
            'phone' => '081234567893',
            'is_active' => true,
        ]);
        $pembimbing2->assignRole('dosen_pembimbing');

        // Dosen Penguji
        $penguji1 = User::create([
            'name' => 'Dr. Ir. Dewi Kartika',
            'email' => 'penguji1@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => '198002102005012001',
            'phone' => '081234567894',
            'is_active' => true,
        ]);
        $penguji1->assignRole('dosen_penguji');

        $penguji2 = User::create([
            'name' => 'Dr. Yoga Pratama',
            'email' => 'penguji2@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => '197707202002121001',
            'phone' => '081234567895',
            'is_active' => true,
        ]);
        $penguji2->assignRole('dosen_penguji');

        // Mahasiswa
        $mahasiswa1 = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'mahasiswa1@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => '2019010001',
            'phone' => '081234567896',
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'is_active' => true,
        ]);
        $mahasiswa1->assignRole('mahasiswa');

        $mahasiswa2 = User::create([
            'name' => 'Rina Kusuma',
            'email' => 'mahasiswa2@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => '2019010002',
            'phone' => '081234567897',
            'address' => 'Jl. Thamrin No. 456, Jakarta',
            'is_active' => true,
        ]);
        $mahasiswa2->assignRole('mahasiswa');

        $mahasiswa3 = User::create([
            'name' => 'Dimas Prakoso',
            'email' => 'mahasiswa3@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => '2019010003',
            'phone' => '081234567898',
            'address' => 'Jl. Gatot Subroto No. 789, Jakarta',
            'is_active' => true,
        ]);
        $mahasiswa3->assignRole('mahasiswa');
    }
}
