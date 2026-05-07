<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Le nom du modèle correspondant à la factory.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Statuts possibles pour un employé
        $statuses = ['actif', 'inactif', 'suspendu'];
        
        return [
            'user_id' => null, // Sera lié à un utilisateur si nécessaire
            'matricule' => 'EMP-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->streetAddress() . ', ' . $this->faker->city(),
            'position_id' => Position::inRandomOrder()->first()->id,
            'salary_base' => $this->faker->numberBetween(150000, 1500000), // Salaire entre 150k et 1.5M XOF
            'status' => $this->faker->randomElement($statuses),
        ];
    }

    /**
     * État : Employé actif
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, ['status' => 'actif']));
    }

    /**
     * État : Employé inactif
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, ['status' => 'inactif']));
    }

    /**
     * État : Employé suspendu
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, ['status' => 'suspendu']));
    }

    /**
     * État : Employé avec salaire élevé
     */
    public function highSalary(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'salary_base' => $this->faker->numberBetween(1000000, 3000000) // Salaire entre 1M et 3M XOF
        ]));
    }

    /**
     * État : Employé récemment embauché
     */
    public function recentlyHired(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'status' => 'actif' // Les employés récemment embauchés sont généralement actifs
        ]));
    }

    /**
     * État : Employé avec beaucoup d'ancienneté
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => array_merge($attributes, [
            'salary_base' => $this->faker->numberBetween(300000, 800000) // Les employés seniors ont généralement de meilleurs salaires
        ]));
    }
}
