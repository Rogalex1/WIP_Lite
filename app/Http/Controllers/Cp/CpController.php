<?php

namespace App\Http\Controllers\Cp;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Campaign;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Notification;

class CpController extends Controller
{
public function index()
{
    $cpEmployee = Employee::where('user_id', auth()->id())->first();

    // 1. Assignments du CP
    $assignments = Assignment::with(['employee', 'campaign'])
        ->where('manager_id', $cpEmployee->id)
        ->latest()
        ->get();

    // 2. Employés du CP
    $employees = Employee::whereHas('assignments', function ($q) use ($cpEmployee) {
        $q->where('manager_id', $cpEmployee->id);
    })->get();

    // 3. PLANNINGS (IMPORTANT)
    // 👉 si planning est dans campaign
    $plannings = Assignment::with(['campaign'])
        ->where('manager_id', $cpEmployee->id)
        ->whereHas('campaign', function ($q) {
            $q->whereNotNull('id'); // adapte si status planning existe
        })
        ->get();

    return Inertia::render('Dashboard/CPDashboard', [
        'stats' => [
            'employees_count' => $employees->count(),
            'campaigns_count' => $assignments->pluck('campaign_id')->unique()->count(),
            'pending_count'   => $assignments->where('status', 'pending')->count(),
        ],

        'my_teams' => $employees,

        // 👇 AJOUT IMPORTANT
        'assignments' => $assignments,
        'plannings' => $plannings,
    ]);
}
}
