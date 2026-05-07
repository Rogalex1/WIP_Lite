<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assignment;
use App\Models\Employee;
use App\Models\Campaign;
use App\Models\Position;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les modèles nécessaires en gérant les cas où ils n'existent pas
        $emp1 = Employee::where('matricule', 'EMP0001')->first();
        $emp2 = Employee::where('matricule', 'EMP0002')->first();
        $emp3 = Employee::where('matricule', 'EMP0003')->first();
        $emp4 = Employee::where('matricule', 'EMP0004')->first();
        
        $campaign1 = Campaign::where('name', 'Refonte Site E-commerce')->first();
        $campaign2 = Campaign::where('name', 'Application Mobile Banking')->first();
        $campaign3 = Campaign::where('name', 'CRM Integration')->first();
        
        $posRH = Position::where('code', 'RH')->first();
        $posCP = Position::where('code', 'CP')->first();
        $posSUP = Position::where('code', 'SUP')->first();

        $assignments = [];
        
        // Créer les affectations seulement si tous les modèles existent
        if ($emp1 && $emp2 && $campaign1 && $posRH) {
            $assignments[] = [
                'employee_id' => $emp1->id,
                'campaign_id' => $campaign1->id,
                'manager_id' => $emp2->id,
                'position_id' => $posRH->id,
                'status' => 'actif',
                'start_date' => '2024-01-15',
                'end_date' => '2024-06-30',
            ];
        }
        
        if ($emp3 && $emp2 && $campaign1 && $posCP) {
            $assignments[] = [
                'employee_id' => $emp3->id,
                'campaign_id' => $campaign1->id,
                'manager_id' => $emp2->id,
                'position_id' => $posCP->id,
                'status' => 'actif',
                'start_date' => '2024-01-20',
                'end_date' => '2024-06-30',
            ];
        }
        
        if ($emp4 && $emp1 && $campaign2 && $posSUP) {
            $assignments[] = [
                'employee_id' => $emp4->id,
                'campaign_id' => $campaign2->id,
                'manager_id' => $emp1->id,
                'position_id' => $posSUP->id,
                'status' => 'actif',
                'start_date' => '2024-02-01',
                'end_date' => '2024-12-31',
            ];
        }
        
        if ($emp3 && $emp4 && $campaign2 && $posCP) {
            $assignments[] = [
                'employee_id' => $emp3->id,
                'campaign_id' => $campaign2->id,
                'manager_id' => $emp4->id,
                'position_id' => $posCP->id,
                'status' => 'actif',
                'start_date' => '2024-02-15',
                'end_date' => '2024-12-31',
            ];
        }
        
        if ($emp4 && $emp2 && $campaign3 && $posSUP) {
            $assignments[] = [
                'employee_id' => $emp4->id,
                'campaign_id' => $campaign3->id,
                'manager_id' => $emp2->id,
                'position_id' => $posSUP->id,
                'status' => 'actif',
                'start_date' => '2024-03-15',
                'end_date' => '2024-08-15',
            ];
        }

        foreach ($assignments as $assignment) {
            Assignment::create($assignment);
        }
    }
}
