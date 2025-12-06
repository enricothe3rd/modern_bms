<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sector>
 */
class SectorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Public Administration',
                'Education',
                'Health Services',
                'Infrastructure',
                'Agriculture',
                'Technology',
                'Finance',
                'Defense',
                'Environment',
                'Social Services'
            ])
        ];
    }
}
