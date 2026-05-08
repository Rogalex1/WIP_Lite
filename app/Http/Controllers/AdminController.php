<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Campaign;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Notification;


class AdminController extends Controller
{

    public function index()
    {
        return Inertia::render('Dashboard/AdminDashboard', [
            'stats' => [
                'employees_count' => Employee::count(),
                'campaigns_count' => Campaign::where('status', 'active')->count(), // Ajuste selon tes colonnes
                'pending_count' => Employee::whereDoesntHave('assignments')->count(), // Ceux qui n'ont pas de mission
                'alerts_count' => 5, // Tu pourras lier ça à une logique d'erreurs plus tard
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
                    'status' => $assign->employee->status, // Ou une logique de statut réelle
                ]),
        ]);
    }

    public function no_user(Request $request)
    {
        // 1. Charger les employés SANS compte + leur position
        $employesSansCompte = Employee::with('position')
            ->whereNull('user_id')
            ->get();

        // 2. Employé sélectionné
        $employeSelectionne = null;

        if ($request->has('employe_id')) {
            $employeSelectionne = Employee::with('position')
                ->whereNull('user_id')
                ->findOrFail($request->employe_id);
        }

        return Inertia::render('Users/No_users', [
            'employesSansCompte' => $employesSansCompte,
            'employe' => $employeSelectionne,
            'roles' => Role::whereIn('name', ['cp', 'sup', 'tc'])->get()
        ]);
    }


    public function create()
    {
    }
}
