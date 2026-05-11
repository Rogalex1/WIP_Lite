<?php
 
namespace Database\Factories;
 
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Position;
 
class EmployeeFactory extends Factory

{

    public function definition(): array

    {

        return [

            'user_id' => null,

            'matricule' => 'EMP' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),

            'first_name' => fake()->firstName(),

            'last_name' => fake()->lastName(),

            'birth_date' => fake()->date('Y-m-d', '-18 years'),

            'phone' => fake()->phoneNumber(),

            'email' => fake()->unique()->safeEmail(),

            'address' => fake()->address(),

            'position_id' => Position::inRandomOrder()->first()->id,

            'salary_base' => fake()->randomFloat(2, 25000, 120000),

            'status' => fake()->randomElement(['actif', 'inactif', 'suspendu']),

        ];

    }
 
    public function active(): static

    {

        return $this->state(['status' => 'actif']);

    }
 
    public function inactive(): static

    {

        return $this->state(['status' => 'inactif']);

    }
 
    public function suspended(): static

    {

        return $this->state(['status' => 'suspendu']);

    }
 
    public function highSalary(): static

    {

        return $this->state([

            'salary_base' => fake()->randomFloat(2, 100000, 200000),

        ]);

    }
 
    public function recentlyHired(): static

    {

        return $this->state([

            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),

        ]);

    }
 
    public function senior(): static

    {

        return $this->state([

            'created_at' => fake()->dateTimeBetween('-10 years', '-5 years'),

            'birth_date' => fake()->date('Y-m-d', '-40 years'),

        ]);

    }

}
 