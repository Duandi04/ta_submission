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
        $data = [
            'FB' => [
                ['code' => 'AKT', 'name' => 'Akuntansi'],
                ['code' => 'MNJ', 'name' => 'Manajemen'],
            ],
            'FAKOM' => [
                ['code' => 'SI', 'name' => 'Sistem Informasi'],
                ['code' => 'IF', 'name' => 'Teknik Informatika'],
                ['code' => 'TPL', 'name' => 'Teknik Perangkat Lunak'],
            ],
            'FPBB' => [
                ['code' => 'PBM', 'name' => 'Pendidikan Bahasa Mandarin'],
            ],
            'FTEK' => [
                ['code' => 'TID', 'name' => 'Teknik Industri'],
            ],
        ];
 
        foreach ($data as $facultyCode => $prodis) {
            $faculty = Faculty::where('code', $facultyCode)->first();
            if ($faculty) {
                foreach ($prodis as $prodiData) {
                    ProgramStudi::updateOrCreate(
                        ['code' => $prodiData['code']],
                        ['name' => $prodiData['name'], 'faculty_id' => $faculty->id]
                    );
                }
            }
        }
    }
}
