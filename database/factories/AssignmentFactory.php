<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;
use App\Models\Campaign;
use App\Models\Position;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+1 year');
        
        return [
            'employee_id' => Employee::factory(),
            'campaign_id' => Campaign::factory(),
            'manager_id' => Employee::inRandomOrder()->first()->id,
            'position_id' => Position::inRandomOrder()->first()->id,
            'status' => fake()->randomElement(['active', 'inactive', 'completed']),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => fake()->randomElement([null, $endDate->format('Y-m-d')]),
        ];
    }
}
