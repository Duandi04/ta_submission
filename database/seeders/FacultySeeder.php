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
            ['name' => 'Fakultas Bisnis', 'code' => 'FB'],
            ['name' => 'Fakultas Komputer', 'code' => 'FAKOM'],
            ['name' => 'Fakultas Pendidikan, Bahasa, dan Budaya', 'code' => 'FPBB'],
            ['name' => 'Fakultas Teknik', 'code' => 'FT'],
        ];
 
        foreach ($faculties as $faculty) {
            Faculty::updateOrCreate(['code' => $faculty['code']], $faculty);
        }
    }
}
