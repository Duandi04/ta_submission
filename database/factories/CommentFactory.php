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
    protected static array $supervisorComments = [
        'Bagus, silakan lanjutkan ke tahap berikutnya.',
        'Perlu perbaikan pada bagian metodologi. Jelaskan lebih detail langkah-langkahnya.',
        'Referensi yang digunakan sudah baik, tetapi tambahkan beberapa jurnal internasional.',
        'BAB I sudah cukup baik, silakan lanjut ke BAB II.',
        'Tolong perbaiki format penulisan sesuai dengan pedoman penulisan TA.',
        'Hasil pengujian sudah bagus. Tambahkan analisis terhadap hasil tersebut.',
        'Diagram alir sudah benar, tetapi perlu penjelasan lebih lanjut di narasi.',
        'Latar belakang masalah perlu diperkuat dengan data pendukung.',
        'Silakan revisi dan upload ulang dokumen yang sudah diperbaiki.',
        'Sudah disetujui untuk lanjut ke tahap sidang.',
    ];

    protected static array $examinerComments = [
        'Bagaimana jika dibandingkan dengan metode lain yang serupa?',
        'Apa keunggulan sistem yang dikembangkan dibanding sistem yang sudah ada?',
        'Tolong jelaskan lebih detail tentang arsitektur sistem yang digunakan.',
        'Bagaimana sistem menangani jika terjadi error atau exception?',
        'Apakah sudah dilakukan pengujian dengan skenario edge case?',
        'Berapa waktu rata-rata yang dibutuhkan sistem untuk memproses request?',
        'Bagaimana rencana pengembangan sistem ke depannya?',
        'Apa saja keterbatasan dari penelitian ini?',
    ];

    protected static array $studentReplies = [
        'Baik Pak/Bu, akan saya perbaiki segera.',
        'Terima kasih atas masukannya, akan saya tambahkan.',
        'Sudah saya revisi sesuai saran, mohon dicek kembali.',
        'Siap, akan saya upload revisinya maksimal besok.',
        'Mengenai hal tersebut, penjelasannya ada di halaman 45.',
        'Sudah saya tambahkan di BAB III bagian 3.2.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thesis_submission_id' => ThesisSubmission::factory(),
            'user_id' => User::factory(),
            'content' => fake()->randomElement(self::$supervisorComments),
            'parent_id' => null,
        ];
    }

    /**
     * From supervisor
     */
    public function fromSupervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->randomElement(self::$supervisorComments),
        ]);
    }

    /**
     * From examiner
     */
    public function fromExaminer(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->randomElement(self::$examinerComments),
        ]);
    }

    /**
     * Student reply
     */
    public function studentReply(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->randomElement(self::$studentReplies),
        ]);
    }

    /**
     * As a reply to another comment
     */
    public function replyTo(Comment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'thesis_submission_id' => $parent->thesis_submission_id,
            'parent_id' => $parent->id,
        ]);
    }

    /**
     * For specific thesis
     */
    public function forThesis(ThesisSubmission $thesis): static
    {
        return $this->state(fn (array $attributes) => [
            'thesis_submission_id' => $thesis->id,
        ]);
    }

    /**
     * From specific user
     */
    public function fromUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
