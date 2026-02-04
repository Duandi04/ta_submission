<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FacultySeeder::class,
            ProgramStudiSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            RubricSeeder::class,
            ThesisSubmissionSeeder::class,
        ]);
    }
}
