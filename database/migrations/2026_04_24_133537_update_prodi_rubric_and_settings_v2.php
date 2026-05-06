<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add deadlines to program_studis
        Schema::table('program_studis', function (Blueprint $table) {
            $table->dateTime('submission_start')->nullable();
            $table->dateTime('submission_end')->nullable();
        });

        // Add program_studi_id to rubrics
        Schema::table('rubrics', function (Blueprint $table) {
            $table->unsignedBigInteger('program_studi_id')->nullable()->after('id');
            $table->foreign('program_studi_id')->references('id')->on('program_studis')->onDelete('cascade');
        });

        // Add settings for submission and draft limits
        DB::table('settings')->updateOrInsert(
            ['key' => 'max_submissions'],
            ['value' => '3', 'description' => 'Maksimal jumlah pengajuan (slot) per mahasiswa.', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('settings')->updateOrInsert(
            ['key' => 'max_drafts_per_submission'],
            ['value' => '3', 'description' => 'Maksimal jumlah draft file per satu pengajuan.', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_studis', function (Blueprint $table) {
            $table->dropColumn(['submission_start', 'submission_end']);
        });

        Schema::table('rubrics', function (Blueprint $table) {
            $table->dropForeign(['program_studi_id']);
            $table->dropColumn('program_studi_id');
        });

        DB::table('settings')->whereIn('key', ['max_submissions', 'max_drafts_per_submission'])->delete();
    }
};
