<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = [
            ['name' => 'Fakultas Teknik', 'code' => 'FT'],
            ['name' => 'Fakultas Ekonomi dan Bisnis', 'code' => 'FEB'],
            ['name' => 'Fakultas Hukum', 'code' => 'FH'],
            ['name' => 'Fakultas Ilmu Komputer', 'code' => 'FIK'],
        ];

        foreach ($faculties as $faculty) {
            Faculty::updateOrCreate(['code' => $faculty['code']], $faculty);
        }
    }
}
