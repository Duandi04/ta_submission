<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'max_thesis_drafts'],
            [
                'value' => '3',
                'description' => 'Maximum number of thesis drafts a student can upload'
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
