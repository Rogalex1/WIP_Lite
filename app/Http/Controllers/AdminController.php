<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentHistory;
use App\Models\Campaign;
use App\Models\Employee;
use App\Models\Position;
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
                'campaigns_count' => Campaign::where('status', 'active')->count(),
                'pending_count'   => Assignment::where('status', 'active')->count(),
                'alerts_count'    => Employee::where('status', 'non affecté')->count(),
            ],
            'recent_assignments' => Assignment::where('status', 'active')
                ->with(['employee.position', 'campaign'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($assign) => [
                    'id' => $assign->id,
                    'employee_name' => $assign->employee->first_name . ' ' . $assign->employee->last_name,
                    'role' => $assign->employee->position->name ?? 'Non défini',
                    'campaign_name' => $assign->campaign->name,
                    'status' => $assign->status,
                ]),
            'recent_history' => AssignmentHistory::with(['employee', 'author', 'newCampaign'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($h) => [
                    'id' => $h->id,
                    'employee_name' => trim((optional($h->employee)->first_name ?? '') . ' ' . (optional($h->employee)->last_name ?? '')) ?: 'Employé inconnu',
                    'action' => $h->action_type,
                    'campaign' => $h->newCampaign->name ?? 'N/A',
                    'author' => optional($h->author)->name ?? 'Système',
                    'date' => $h->created_at->format('d/m/Y H:i'),
                    'reason' => $h->reason
                ])
        ]);
    }

    public function history()
    {
        $history = AssignmentHistory::with(['employee', 'author', 'newCampaign', 'oldCampaign', 'newManager', 'oldManager'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/History', [
            'history' => $history
        ]);
    }

    public function no_user(Request $request)
    {
        $employesSansCompte = Employee::whereNull('user_id')
            ->with('position')
            ->get();
        
        return Inertia::render('Users/No_users', [
            'employesSansCompte' => $employesSansCompte,
            'positions' => Position::all(),
            'roles' => Role::whereIn('name', ['cp', 'sup', 'tc'])->get(),
        ]);
    }
}
