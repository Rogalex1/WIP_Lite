<?php

namespace App\Http\Controllers\Sup;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Campaign;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Notification;

class SupController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        // Campagnes où il est SUP
        $campaignIds = Assignment::where('employee_id', $employee->id)
            ->where('position_id', 2)
            ->where('status', 'active')
            ->pluck('campaign_id');

        // Agents sous sa supervision directe
        $myAgents = Assignment::where('manager_id', $employee->id)
            ->where('status', 'active')
            ->where('position_id', 3) // TC
            ->with(['employee', 'campaign'])
            ->get()
            ->map(fn($assign) => [
                'id' => $assign->employee->id,
                'name' => $assign->employee->first_name . ' ' . $assign->employee->last_name,
                'last_action' => 'En production sur ' . $assign->campaign->name,
                'campaign_id' => $assign->campaign_id,
            ]);

        return Inertia::render('Dashboard/SUPDashboard', [
            'stats' => [
                'present_count' => $myAgents->count(),
                'absent_count' => 0, // À lier plus tard
                'total_team' => $myAgents->count(),
            ],
            'my_agents' => $myAgents,
        ]);
    }
}
