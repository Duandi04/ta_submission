<?php

namespace Database\Factories;

use App\Models\AssessmentScore;
use App\Models\Assessment;
use App\Models\AssessmentCriterion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssessmentScore>
 */
class AssessmentScoreFactory extends Factory
{
    protected static array $scoreNotes = [
        'excellent' => [
            'Sangat baik, melebihi ekspektasi.',
            'Luar biasa, tidak ada yang perlu diperbaiki.',
            'Excellent work!',
        ],
        'good' => [
            'Baik, sesuai standar.',
            'Sudah bagus, hanya perlu penyempurnaan minor.',
            'Good job, keep it up.',
        ],
        'average' => [
            'Cukup, perlu sedikit perbaikan.',
            'Memenuhi standar minimum.',
            'Acceptable, room for improvement.',
        ],
        'below_average' => [
            'Perlu perbaikan signifikan.',
            'Belum memenuhi standar yang diharapkan.',
            'Needs more work.',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $score = fake()->randomFloat(2, 50, 100);
        $category = $this->getScoreCategory($score);
        
        return [
            'assessment_id' => Assessment::factory(),
            'criterion_id' => AssessmentCriterion::factory(),
            'criterion_name' => fake()->words(3, true),
            'criterion_description' => fake()->sentence(),
            'weight' => fake()->randomFloat(2, 5, 30),
            'score' => $score,
            'notes' => fake()->randomElement(self::$scoreNotes[$category]),
        ];
    }

    /**
     * Get category based on score
     */
    protected function getScoreCategory(float $score): string
    {
        return match (true) {
            $score >= 85 => 'excellent',
            $score >= 70 => 'good',
            $score >= 55 => 'average',
            default => 'below_average',
        };
    }

    /**
     * High score (85-100)
     */
    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->randomFloat(2, 85, 100),
            'notes' => fake()->randomElement(self::$scoreNotes['excellent']),
        ]);
    }

    /**
     * Good score (70-84)
     */
    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->randomFloat(2, 70, 84.99),
            'notes' => fake()->randomElement(self::$scoreNotes['good']),
        ]);
    }

    /**
     * Average score (55-69)
     */
    public function average(): static
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->randomFloat(2, 55, 69.99),
            'notes' => fake()->randomElement(self::$scoreNotes['average']),
        ]);
    }

    /**
     * For specific assessment
     */
    public function forAssessment(Assessment $assessment): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_id' => $assessment->id,
        ]);
    }

    /**
     * For specific criterion
     */
    public function forCriterion(AssessmentCriterion $criterion): static
    {
        return $this->state(fn (array $attributes) => [
            'criterion_id' => $criterion->id,
        ]);
    }
}
