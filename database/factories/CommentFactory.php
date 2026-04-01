<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $indonesianWords = [
            'Bagus', 'silakan', 'perbaikan', 'metodologi', 'referensi',
            'Terima kasih', 'revisi', 'Bagaimana', 'jelaskan', 'pengujian'
        ];

        return [
            'thesis_submission_id' => ThesisSubmission::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'content' => fake()->randomElement($indonesianWords) . ' ' . fake()->sentence(),
        ];
    }

    public function forThesis(ThesisSubmission $thesis): static
    {
        return $this->state(fn (array $attributes) => [
            'thesis_submission_id' => $thesis->id,
        ]);
    }

    public function fromUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function replyTo(Comment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'thesis_submission_id' => $parent->thesis_submission_id,
        ]);
    }

    public function fromSupervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->randomElement(['Bagus, silakan lanjutkan.', 'Perlu perbaikan pada metodologi.', 'Tambahkan referensi untuk BAB 2.', 'Format sudah disetujui.', 'Mohon perbaiki diagram.', 'Ada beberapa hasil yang perlu direvisi.']),
        ]);
    }

    public function fromExaminer(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->randomElement(['Bagaimana keunggulan metode ini?', 'Tolong jelaskan arsitektur sistem.', 'Ada pesan error saat pengujian.', 'Waktu respon masih lambat.', 'Apa rencana untuk mengatasi keterbatasan ini?']),
        ]);
    }

    public function studentReply(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->randomElement(['Baik, terima kasih.', 'Sudah saya revisi dan upload ulang.', 'Berikut penjelasan tambahannya.', 'Akan saya tambahkan.']),
        ]);
    }
}
