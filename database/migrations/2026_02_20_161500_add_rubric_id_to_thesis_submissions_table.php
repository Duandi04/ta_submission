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
        Schema::table('thesis_submissions', function (Blueprint $column) {
            $column->foreignId('rubric_id')->nullable()->after('supervisor_id')->constrained('rubrics')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thesis_submissions', function (Blueprint $column) {
            $column->dropForeign(['rubric_id']);
            $column->dropColumn('rubric_id');
        });
    }
};
