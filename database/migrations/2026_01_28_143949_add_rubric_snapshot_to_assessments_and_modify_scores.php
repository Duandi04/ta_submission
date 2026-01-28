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
        Schema::table('assessments', function (Blueprint $table) {
            $table->json('rubric_snapshot')->nullable()->after('rubric_id');
        });

        // Remove foreign key constraint from assessment_scores if it exists
        // We will store just the ID/Key from the json snapshot
        Schema::table('assessment_scores', function (Blueprint $table) {
            $table->dropForeign(['criterion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_scores', function (Blueprint $table) {
            // Re-adding FK might fail if there are invalid IDs now, but for rollback:
            // $table->foreign('criterion_id')->references('id')->on('assessment_criteria');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('rubric_snapshot');
        });
    }
};
