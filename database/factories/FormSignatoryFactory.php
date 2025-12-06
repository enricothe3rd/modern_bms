<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormSignatory>
 */
class FormSignatoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => \App\Models\Form::factory(),
            'form_name' => null, // Keep for backward compatibility during migration
            'department_id' => Department::factory(),
            'signatory_id' => User::factory(),
            'order' => $this->faker->numberBetween(1, 5)
        ];
    }
}
