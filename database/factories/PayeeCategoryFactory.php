<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PayeeCategory>
 */
class PayeeCategoryFactory extends Factory
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
                'Individual',
                'Company',
                'Government Agency',
                'Non-Profit Organization',
                'Educational Institution',
                'Healthcare Provider',
                'Contractor',
                'Supplier',
                'Consultant',
                'Service Provider'
            ]),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
