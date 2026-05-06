<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
    public function run(): void
    {
        $programStudis = \App\Models\ProgramStudi::all();

        foreach ($programStudis as $prodi) {
            \App\Models\Rubric::updateOrCreate(
                ['name' => 'Rubrik Penilaian Proposal TA - ' . $prodi->code],
                [
                    'program_studi_id' => $prodi->id,
                    'description' => 'Rubrik standar untuk penilaian proposal Tugas Akhir program studi ' . $prodi->name,
                    'criteria' => [
                        ['name' => 'Latar Belakang', 'weight' => 20, 'description' => 'Kualitas argumen dan urgensi penelitian'],
                        ['name' => 'Rumusan Masalah', 'weight' => 15, 'description' => 'Kualitas perumusan masalah'],
                        ['name' => 'Tinjauan Pustaka', 'weight' => 25, 'description' => 'Kelengkapan dan relevansi pustaka'],
                        ['name' => 'Metodologi', 'weight' => 30, 'description' => 'Ketepatan metode yang diusulkan'],
                        ['name' => 'Bahasa & Penulisan', 'weight' => 10, 'description' => 'Kualitas penulisan dan tata bahasa'],
                    ],
                    'is_active' => true
                ]
            );
        }
    }
}
