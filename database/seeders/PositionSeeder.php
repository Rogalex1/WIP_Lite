<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['name' => 'Ressources Humaines', 'code' => 'RH', 'description' => 'Gestion des ressources humaines et administration du personnel'],
            ['name' => 'ChefProjet', 'code' => 'CP', 'description' => 'Responsable de la gestion de projet et coordination des équipes'],
            ['name' => 'Superviseur', 'code' => 'SUP', 'description' => 'Supervision des équipes et suivi des performances'],
            ['name' => 'Téléconseiller', 'code' => 'TC', 'description' => 'Conseiller client par téléphone et support à distance'],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
