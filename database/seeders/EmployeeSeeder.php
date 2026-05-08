<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Position;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des employés de test simples différents profils
        $this->createActiveEmployees();
        $this->createInactiveEmployees();
        $this->createSuspendedEmployees();
        $this->createHighSalaryEmployees();
        $this->createRecentlyHiredEmployees();
        $this->createSeniorEmployees();
        $this->createTestEmployees();

        $this->command->info('Employés créés avec succès!');
    }

    /**
     * Créer des employés actifs
     */
    private function createActiveEmployees(): void
    {
        Employee::factory()
            ->count(200)
            ->active()
            ->create();
    }

    /**
     * Créer des employés inactifs
     */
    private function createInactiveEmployees(): void
    {
        Employee::factory()
            ->count(50)
            ->inactive()
            ->create();
    }

    /**
     * Créer des employés suspendus
     */
    private function createSuspendedEmployees(): void
    {
        Employee::factory()
            ->count(10)
            ->suspended()
            ->create();
    }

    /**
     * Créer des employés avec salaires élevés
     */
    private function createHighSalaryEmployees(): void
    {
        Employee::factory()
            ->count(20)
            ->highSalary()
            ->active()
            ->create();
    }

    /**
     * Créer des employés récemment embauchés
     */
    private function createRecentlyHiredEmployees(): void
    {
        Employee::factory()
            ->count(40)
            ->recentlyHired()
            ->active()
            ->create();
    }

    /**
     * Créer des employés seniors (avec beaucoup d'ancienneté)
     */
    private function createSeniorEmployees(): void
    {
        Employee::factory()
            ->count(30)
            ->senior()
            ->active()
            ->create();
    }

    /**
     * Créer des employés de test spécifiques
     */
    private function createTestEmployees(): void
    {
        Employee::factory()->create([
    'matricule' => 'EMP0001',
    'first_name' => 'Jean',
    'last_name' => 'Dupont',
    'email' => 'jean.dupont@entreprise.com',
    'phone' => '+225 07 00 00 01',
    'address' => '123 Rue de la République, Abidjan',
    'position_id' => Position::where('code', 'CP')->first()->id,
    'salary_base' => 500000,
    'status' => 'actif',
]);

Employee::factory()->create([
    'matricule' => 'EMP0002',
    'first_name' => 'Marie',
    'last_name' => 'Curie',
    'email' => 'marie.curie@entreprise.com',
    'phone' => '+225 07 00 00 02',
    'address' => '456 Avenue des Champs-Élysées, Abidjan',
    'position_id' => Position::where('code', 'RH')->first()->id,
    'salary_base' => 750000,
    'status' => 'actif',
]);

Employee::factory()->create([
    'matricule' => 'EMP0003',
    'first_name' => 'Paul',
    'last_name' => 'Koffi',
    'email' => 'paul.koffi@entreprise.com',
    'phone' => '+225 07 00 00 03',
    'address' => 'Abidjan',
    'position_id' => Position::where('code', 'CP')->first()->id,
    'salary_base' => 400000,
    'status' => 'actif',
]);

Employee::factory()->create([
    'matricule' => 'EMP0004',
    'first_name' => 'Awa',
    'last_name' => 'Traoré',
    'email' => 'awa.traore@entreprise.com',
    'phone' => '+225 07 00 00 04',
    'address' => 'Abidjan',
    'position_id' => Position::where('code', 'RH')->first()->id,
    'salary_base' => 450000,
    'status' => 'actif',
]);

    }
}
