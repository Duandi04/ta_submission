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
        Schema::table('faculties', function (Blueprint $table) {
            $table->text('description')->nullable()->after('code');
            $table->string('website')->nullable()->after('description');
        });

        Schema::table('program_studis', function (Blueprint $table) {
            $table->text('description')->nullable()->after('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            $table->dropColumn(['description', 'website']);
        });

        Schema::table('program_studis', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
