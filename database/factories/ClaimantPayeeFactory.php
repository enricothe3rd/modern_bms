<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\PayeeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClaimantPayee>
 */
class ClaimantPayeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'department_id' => Department::factory(),
            'payee_category_id' => PayeeCategory::factory(),
        ];
    }
}
