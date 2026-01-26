<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ft = Faculty::where('code', 'FT')->first();
        $fik = Faculty::where('code', 'FIK')->first();

        if ($ft) {
            ProgramStudi::updateOrCreate(['code' => 'IF'], ['name' => 'Teknik Informatika', 'faculty_id' => $ft->id]);
            ProgramStudi::updateOrCreate(['code' => 'SI'], ['name' => 'Sistem Informasi', 'faculty_id' => $ft->id]);
        }

        if ($fik) {
            ProgramStudi::updateOrCreate(['code' => 'TI'], ['name' => 'Teknologi Informasi', 'faculty_id' => $fik->id]);
        }
    }
}
