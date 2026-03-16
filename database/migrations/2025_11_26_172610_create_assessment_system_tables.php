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
        Schema::create('rubrics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('criteria'); // Stores rubric items, weights, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rubric_id')->nullable()->constrained()->nullOnDelete();
            $table->json('rubric_snapshot')->nullable();
            $table->decimal('total_score', 5, 2)->nullable();
            $table->text('comments')->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Ensure one assessment per evaluator per submission
            $table->unique(['thesis_submission_id', 'evaluator_id']);
        });

        Schema::create('assessment_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_score')->default(100);
            $table->integer('weight_percentage')->default(100);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('criterion_id'); // No FK as we might refer to snapshot
            $table->decimal('score', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_scores');
        Schema::dropIfExists('assessment_criteria');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('rubrics');
    }
};
