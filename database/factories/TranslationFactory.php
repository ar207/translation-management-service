<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->slug,
            'locale_id' => $this->faker->randomElement(['1', '2', '3']),
            'content' => $this->faker->sentence,
            'tags' => [$this->faker->randomElement(['web', 'mobile', 'desktop'])],
        ];
    }
}
