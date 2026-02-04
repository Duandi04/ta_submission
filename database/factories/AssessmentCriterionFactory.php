<?php

namespace Database\Factories;

use App\Models\AssessmentCriterion;
use App\Models\Rubric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssessmentCriterion>
 */
class AssessmentCriterionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rubric_id' => Rubric::factory(),
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'weight' => fake()->numberBetween(10, 30),
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    /**
     * State for inactive criterion
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
