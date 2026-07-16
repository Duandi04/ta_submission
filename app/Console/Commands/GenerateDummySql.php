<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class GenerateDummySql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:generate-dummy-sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate database.sql containing specific dummy data for TPL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate database.sql...');

        $tempDbPath = storage_path('app/temp_dummy.sqlite');
        
        // Ensure old temp file is removed
        if (File::exists($tempDbPath)) {
            File::delete($tempDbPath);
        }
        
        File::put($tempDbPath, '');

        // Temporarily configure a new database connection
        Config::set('database.connections.sqlite_temp', [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => $tempDbPath,
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]);

        $defaultConnection = Config::get('database.default');
        $defaultCache = Config::get('cache.default');
        Config::set('database.default', 'sqlite_temp');
        Config::set('cache.default', 'array');

        try {
            $this->info('Migrating schema to temporary SQLite database...');
            Artisan::call('migrate:fresh', [
                '--database' => 'sqlite_temp',
                '--force' => true
            ]);

            $this->info('Seeding data...');
            Artisan::call('db:seed', [
                '--class' => 'CustomExportSeeder',
                '--database' => 'sqlite_temp',
                '--force' => true
            ]);

            $this->info('Extracting data to database.sql...');
            $sql = "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tables = [
                'faculties', 'program_studis', 'users', 'roles', 'permissions', 'model_has_roles', 'role_has_permissions',
                'thesis_submissions', 'comments'
            ];

            foreach ($tables as $table) {
                $rows = DB::connection('sqlite_temp')->table($table)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data for $table\n";
                    $sql .= "TRUNCATE TABLE `$table`;\n";
                    
                    foreach ($rows as $row) {
                        $rowArr = (array) $row;
                        $keys = array_keys($rowArr);
                        $values = array_map(function ($value) {
                            if (is_null($value)) return "NULL";
                            return "'" . addslashes((string)$value) . "'";
                        }, array_values($rowArr));

                        $sql .= "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put(base_path('database.sql'), $sql);
            $this->info('Successfully generated database.sql in the project root!');

        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        } finally {
            // Restore default connection
            Config::set('database.default', $defaultConnection);
            Config::set('cache.default', $defaultCache);

            // Clean up temporary database
            if (File::exists($tempDbPath)) {
                File::delete($tempDbPath);
            }
        }
    }
}
