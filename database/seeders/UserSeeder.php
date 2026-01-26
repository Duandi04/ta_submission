<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $if = ProgramStudi::where('code', 'IF')->first();
        $si = ProgramStudi::where('code', 'SI')->first();
        $ti = ProgramStudi::where('code', 'TI')->first();

        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@ta.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'nim_nip' => 'ADM001',
                'phone' => '081234567890',
                'is_active' => true,
                'program_studi_id' => $if->id ?? null,
            ]
        );
        $admin->syncRoles(['admin']);

        // Koordinator
        $koordinator = User::updateOrCreate(
            ['email' => 'koordinator@ta.test'],
            [
                'name' => 'Dr. Budi Santoso',
                'password' => Hash::make('password'),
                'nim_nip' => '197001011995011001',
                'phone' => '081234567891',
                'is_active' => true,
                'program_studi_id' => $if->id ?? null,
            ]
        );
        $koordinator->syncRoles(['koordinator']);

        // Kaprodi
        $kaprodi = User::updateOrCreate(
            ['email' => 'kaprodi@ta.test'],
            [
                'name' => 'Dr. Andi Pratama',
                'password' => Hash::make('password'),
                'nim_nip' => '198001012005011002',
                'phone' => '081234567899',
                'is_active' => true,
                'program_studi_id' => $if->id ?? null,
            ]
        );
        $kaprodi->syncRoles(['kaprodi']);

        // Dosen Pembimbing
        $pembimbing1 = User::updateOrCreate(
            ['email' => 'pembimbing1@ta.test'],
            [
                'name' => 'Dr. Siti Aminah',
                'password' => Hash::make('password'),
                'nim_nip' => '197505151998022001',
                'phone' => '081234567892',
                'is_active' => true,
                'program_studi_id' => $if->id ?? null,
            ]
        );
        $pembimbing1->syncRoles(['dosen_pembimbing']);

        $pembimbing2 = User::updateOrCreate(
            ['email' => 'pembimbing2@ta.test'],
            [
                'name' => 'Prof. Ahmad Rahman',
                'password' => Hash::make('password'),
                'nim_nip' => '196803101992031001',
                'phone' => '081234567893',
                'is_active' => true,
                'program_studi_id' => $si->id ?? null,
            ]
        );
        $pembimbing2->syncRoles(['dosen_pembimbing']);

        // Dosen Penguji
        $penguji1 = User::updateOrCreate(
            ['email' => 'penguji1@ta.test'],
            [
                'name' => 'Dr. Ir. Dewi Kartika',
                'password' => Hash::make('password'),
                'nim_nip' => '198002102005012001',
                'phone' => '081234567894',
                'is_active' => true,
                'program_studi_id' => $if->id ?? null,
            ]
        );
        $penguji1->syncRoles(['dosen_penguji']);

        $penguji2 = User::updateOrCreate(
            ['email' => 'penguji2@ta.test'],
            [
                'name' => 'Dr. Yoga Pratama',
                'password' => Hash::make('password'),
                'nim_nip' => '197707202002121001',
                'phone' => '081234567895',
                'is_active' => true,
                'program_studi_id' => $ti->id ?? null,
            ]
        );
        $penguji2->syncRoles(['dosen_penguji']);

        // Mahasiswa
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa1@ta.test'],
            [
                'name' => 'Andi Wijaya',
                'password' => Hash::make('password'),
                'nim_nip' => '2019010001',
                'phone' => '081234567896',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'is_active' => true,
                'program_studi_id' => $if->id ?? null,
            ]
        );
        $mahasiswa1->syncRoles(['mahasiswa']);

        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'mahasiswa2@ta.test'],
            [
                'name' => 'Rina Kusuma',
                'password' => Hash::make('password'),
                'nim_nip' => '2019010002',
                'phone' => '081234567897',
                'address' => 'Jl. Thamrin No. 456, Jakarta',
                'is_active' => true,
                'program_studi_id' => $si->id ?? null,
            ]
        );
        $mahasiswa2->syncRoles(['mahasiswa']);

        $mahasiswa3 = User::updateOrCreate(
            ['email' => 'mahasiswa3@ta.test'],
            [
                'name' => 'Dimas Prakoso',
                'password' => Hash::make('password'),
                'nim_nip' => '2019010003',
                'phone' => '081234567898',
                'address' => 'Jl. Gatot Subroto No. 789, Jakarta',
                'is_active' => true,
                'program_studi_id' => $ti->id ?? null,
            ]
        );
        $mahasiswa3->syncRoles(['mahasiswa']);
    }
}
