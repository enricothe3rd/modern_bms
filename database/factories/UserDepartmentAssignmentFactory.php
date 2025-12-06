<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDepartmentAssignment;
use App\Models\User;
use App\Models\Department;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDepartmentAssignment>
 */
class UserDepartmentAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserDepartmentAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->optional(0.7)->dateTimeBetween('-1 year', 'now');
        $endDate = $startDate ? $this->faker->optional(0.3)->dateTimeBetween($startDate, '+1 year') : null;

        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'start_date' => $startDate,
            'end_date' => $endDate,
            'notes' => $this->faker->optional(0.4)->sentence(),
        ];
    }

    /**
     * Indicate that the assignment is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the assignment is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the assignment has a specific date range.
     */
    public function withDateRange(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
            return [
                'start_date' => $startDate,
                'end_date' => $this->faker->dateTimeBetween($startDate, '+6 months'),
            ];
        });
    }

    /**
     * Indicate that the assignment has no end date.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => null,
        ]);
    }
}