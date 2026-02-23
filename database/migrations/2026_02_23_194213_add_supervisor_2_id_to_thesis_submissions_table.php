<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('thesis_submissions', function (Blueprint $table) {
            $table->foreignId('supervisor_2_id')->nullable()->after('supervisor_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thesis_submissions', function (Blueprint $table) {
            $table->dropForeign(['supervisor_2_id']);
            $table->dropColumn('supervisor_2_id');
        });
    }
};
