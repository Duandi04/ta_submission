<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\ThesisSubmission;
use App\Models\User;
use App\Models\Rubric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    protected static array $comments = [
        'Penelitian ini menunjukkan pemahaman yang baik terhadap topik yang diangkat.',
        'Metodologi yang digunakan sudah tepat dan sesuai dengan tujuan penelitian.',
        'Perlu ditambahkan analisis yang lebih mendalam pada bagian hasil.',
        'Referensi yang digunakan sudah cukup relevan dan up-to-date.',
        'Penulisan sudah baik, namun perlu perbaikan pada beberapa bagian.',
    ];

    protected static array $strengths = [
        'Topik penelitian sangat relevan dengan kebutuhan industri saat ini.',
        'Implementasi sistem sudah berjalan dengan baik dan stabil.',
        'Analisis data dilakukan secara komprehensif.',
        'Penggunaan teknologi terkini yang sesuai dengan perkembangan zaman.',
        'Dokumentasi lengkap dan terstruktur dengan baik.',
    ];

    protected static array $weaknesses = [
        'Perlu penambahan pengujian untuk edge cases.',
        'Tinjauan pustaka perlu diperkaya dengan referensi internasional.',
        'Beberapa bagian penulisan masih perlu diperbaiki.',
        'Perlu ditambahkan perbandingan dengan metode lain yang sejenis.',
        'Analisis kompleksitas algoritma belum dilakukan.',
    ];

    protected static array $recommendations = [
        'Disarankan untuk melanjutkan penelitian ini ke tahap implementasi yang lebih luas.',
        'Sebaiknya ditambahkan modul keamanan pada sistem yang dikembangkan.',
        'Direkomendasikan untuk publikasi di jurnal nasional terakreditasi.',
        'Perlu dilakukan pengujian dengan dataset yang lebih besar.',
        'Dapat dikembangkan menjadi produk komersial dengan beberapa penyesuaian.',
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
            'evaluator_id' => User::factory(),
            'evaluator_type' => fake()->randomElement(['supervisor', 'examiner_1', 'examiner_2']),
            'rubric_id' => null,
            'total_score' => null,
            'comments' => null,
            'strengths' => null,
            'weaknesses' => null,
            'recommendations' => null,
            'is_submitted' => false,
            'submitted_at' => null,
        ];
    }

    /**
     * Assessment is submitted/completed
     */
    public function submitted(): static
    {
        return $this->state(fn(array $attributes) => [
            'total_score' => fake()->randomFloat(2, 65, 100),
            'comments' => fake()->randomElement(self::$comments),
            'strengths' => fake()->randomElement(self::$strengths),
            'weaknesses' => fake()->randomElement(self::$weaknesses),
            'recommendations' => fake()->randomElement(self::$recommendations),
            'is_submitted' => true,
            'submitted_at' => now()->subDays(rand(1, 14)),
        ]);
    }

    /**
     * Assessment is in draft (not yet submitted)
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'total_score' => null,
            'comments' => null,
            'is_submitted' => false,
            'submitted_at' => null,
        ]);
    }

    /**
     * Supervisor assessment
     */
    public function supervisor(): static
    {
        return $this->state(fn(array $attributes) => [
            'evaluator_type' => 'supervisor',
        ]);
    }

    /**
     * Examiner 1 assessment
     */
    public function examiner1(): static
    {
        return $this->state(fn(array $attributes) => [
            'evaluator_type' => 'examiner_1',
        ]);
    }

    /**
     * Examiner 2 assessment
     */
    public function examiner2(): static
    {
        return $this->state(fn(array $attributes) => [
            'evaluator_type' => 'examiner_2',
        ]);
    }

    /**
     * Configure specific evaluator
     */
    public function forEvaluator(User $evaluator): static
    {
        return $this->state(fn(array $attributes) => [
            'evaluator_id' => $evaluator->id,
        ]);
    }

    /**
     * Configure specific thesis
     */
    public function forThesis(ThesisSubmission $thesis): static
    {
        return $this->state(fn(array $attributes) => [
            'thesis_submission_id' => $thesis->id,
        ]);
    }

    /**
     * With rubric snapshot
     */
    public function withRubric(Rubric $rubric = null): static
    {
        return $this->state(function (array $attributes) use ($rubric) {
            $rubricData = $rubric ?? Rubric::first();
            return [
                'rubric_id' => $rubricData?->id,
            ];
        });
    }
}
