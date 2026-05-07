<?php

namespace App\Http\Controllers\Tc;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PlanningAssignmentController;
use App\Models\Assignment;
use App\Models\Campaign;
use App\Models\Employee;
use App\Models\PlanningAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Notification;

class TcController extends Controller
{
    public function index()
    {
        
        return Inertia::render('Dashboard/TCDashboard', [
        'stats' => [
            'employees_count' => Employee::count(),
            'campaigns_count' => Campaign::where('status', 'active')->count(), // Ajuste selon tes colonnes
            'pending_count'   => Employee::whereDoesntHave('assignments')->count(), // Ceux qui n'ont pas de mission
            'alerts_count'    => 5, // Tu pourras lier ça à une logique d'erreurs plus tard
        ],
        'recent_assignments' => Assignment::with(['employee', 'campaign'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($assign) => [
                'id' => $assign->id,
                'employee_name' => $assign->employee->first_name . ' ' . $assign->employee->last_name,
                'role' => $assign->employee->position->name ?? 'Non défini',
                'campaign_name' => $assign->campaign->name,
                'status' =>  $assign->employee->status, // Ou une logique de statut réelle
            ]),
    ]);
    }

    public function planning()
    {
        return Inertia::render('Dashboard/TCDashboard');
    }

    public function myPlanning()
    {
        $employee = auth()->user()->employee;

        $assignment = PlanningAssignment::with(['planningModel', 'validator'])
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['validé', 'en attente'])
            ->latest()
            ->first();

        return Inertia::render('Planning/TC/MyPlanning', [
            'assignment' => $assignment ? (new \App\Http\Resources\PlanningAssignmentResource($assignment))->resolve() : null,
        ]);
    }
}
