<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'max_batches'],
            [
                'value' => '2',
                'description' => 'Maksimal jumlah batch (siklus) pengajuan yang diizinkan'
            ]
        );
        \App\Models\Setting::updateOrCreate(
            ['key' => 'attempts_per_batch'],
            [
                'value' => '3',
                'description' => 'Maksimal jumlah pengajuan judul dalam satu batch'
            ]
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'campus_name'],
            [
                'value' => 'Universitas Universal',
                'description' => 'Nama institusi kampus'
            ]
        );
    }
}
