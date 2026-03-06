<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submission_files', function (Blueprint $table) {
            $table->string('storage_disk', 20)->default('local')->after('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_files', function (Blueprint $table) {
            $table->dropColumn('storage_disk');
        });
    }
};
