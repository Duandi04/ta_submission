<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Rubric::updateOrCreate(
            ['name' => 'Rubrik Penilaian Proposal TA'],
            [
                'description' => 'Rubrik standar untuk penilaian proposal Tugas Akhir',
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
