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
        Schema::create('thesis_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('abstract');
            $table->string('research_field')->nullable();
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'revision_required',
                'approved',
                'rejected',
                'scheduled_for_defense',
                'defense_in_progress',
                'completed',
                'cancelled'
            ])->default('draft');
            $table->date('submission_date')->nullable();
            $table->date('defense_date')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('thesis_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_submission_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // proposal, final_document, presentation, revision
            $table->integer('file_size');
            $table->string('mime_type');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_files');
        Schema::dropIfExists('thesis_statuses');
        Schema::dropIfExists('thesis_submissions');
    }
};
