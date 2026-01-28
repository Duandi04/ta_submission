<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE thesis_submissions MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'revision_required', 'approved', 'rejected', 'scheduled_for_defense', 'defense_in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Warning: This can fail if there are 'cancelled' rows
        DB::statement("ALTER TABLE thesis_submissions MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'revision_required', 'approved', 'rejected', 'scheduled_for_defense', 'defense_in_progress', 'completed') NOT NULL DEFAULT 'draft'");
    }
};
