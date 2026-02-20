<?php

namespace Database\Factories;

use App\Models\SubmissionFile;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubmissionFile>
 */
class SubmissionFileFactory extends Factory
{
    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (SubmissionFile $file) {
            $sourcePath = database_path('seeders/assets/dummy1.pdf');

            if (File::exists($sourcePath)) {
                Storage::disk('local')->put($file->file_path, File::get($sourcePath));
            }
        });
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileType = fake()->randomElement(['proposal', 'final_document', 'presentation', 'revision']);
        $fileName = $this->generateFileName($fileType);

        return [
            'thesis_submission_id' => ThesisSubmission::factory(),
            'file_name' => $fileName,
            'file_path' => 'submissions/' . date('Y/m') . '/' . $fileName,
            'file_type' => $fileType,
            'file_size' => fake()->numberBetween(100000, 10000000), // 100KB - 10MB
            'mime_type' => 'application/pdf',
            'uploaded_by' => User::factory(),
        ];
    }

    /**
     * Generate appropriate file name based on type
     */
    protected function generateFileName(string $type): string
    {
        $timestamp = now()->format('YmdHis');
        $random = fake()->randomNumber(4, true);

        return match ($type) {
            'proposal' => "proposal_{$timestamp}_{$random}.pdf",
            'final_document' => "dokumen_akhir_{$timestamp}_{$random}.pdf",
            'presentation' => "presentasi_{$timestamp}_{$random}.pptx",
            'revision' => "revisi_{$timestamp}_{$random}.pdf",
            default => "dokumen_{$timestamp}_{$random}.pdf",
        };
    }

    /**
     * Proposal file
     */
    public function proposal(): static
    {
        return $this->state(function (array $attributes) {
            $fileName = $this->generateFileName('proposal');
            return [
                'file_name' => $fileName,
                'file_path' => 'submissions/' . date('Y/m') . '/' . $fileName,
                'file_type' => 'proposal',
                'mime_type' => 'application/pdf',
            ];
        });
    }

    /**
     * Final document
     */
    public function finalDocument(): static
    {
        return $this->state(function (array $attributes) {
            $fileName = $this->generateFileName('final_document');
            return [
                'file_name' => $fileName,
                'file_path' => 'submissions/' . date('Y/m') . '/' . $fileName,
                'file_type' => 'final_document',
                'mime_type' => 'application/pdf',
            ];
        });
    }

    /**
     * Presentation file
     */
    public function presentation(): static
    {
        return $this->state(function (array $attributes) {
            $fileName = $this->generateFileName('presentation');
            return [
                'file_name' => $fileName,
                'file_path' => 'submissions/' . date('Y/m') . '/' . $fileName,
                'file_type' => 'presentation',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ];
        });
    }

    /**
     * Revision file
     */
    public function revision(): static
    {
        return $this->state(function (array $attributes) {
            $fileName = $this->generateFileName('revision');
            return [
                'file_name' => $fileName,
                'file_path' => 'submissions/' . date('Y/m') . '/' . $fileName,
                'file_type' => 'revision',
                'mime_type' => 'application/pdf',
            ];
        });
    }

    /**
     * For specific thesis
     */
    public function forThesis(ThesisSubmission $thesis): static
    {
        return $this->state(fn(array $attributes) => [
            'thesis_submission_id' => $thesis->id,
        ]);
    }

    /**
     * Uploaded by specific user
     */
    public function uploadedBy(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'uploaded_by' => $user->id,
        ]);
    }
}
