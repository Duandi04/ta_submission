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
        // Drop the old assessment_scores table if it exists to start fresh with the new structure
        Schema::dropIfExists('assessment_scores');

        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('criterion_id')->nullable();
            $table->string('criterion_name');
            $table->text('criterion_description')->nullable();
            $table->decimal('weight', 8, 2); // Using weight instead of weight_percentage for consistency
            $table->decimal('score', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Remove rubric_snapshot as we now store details in assessment_scores
        Schema::table('assessments', function (Blueprint $table) {
            if (Schema::hasColumn('assessments', 'rubric_snapshot')) {
                $table->dropColumn('rubric_snapshot');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'rubric_snapshot')) {
                $table->json('rubric_snapshot')->nullable()->after('rubric_id');
            }
        });

        Schema::dropIfExists('assessment_scores');

        // Recreate old structure for rollback
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('criterion_id');
            $table->decimal('score', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
