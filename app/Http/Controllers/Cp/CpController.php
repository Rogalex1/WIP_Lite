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
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        // Campagnes gérées par le CP
        $campaignIds = Assignment::where('employee_id', $employee->id)
            ->where('position_id', 1)
            ->where('status', 'active')
            ->pluck('campaign_id');

        $campaignsCount = $campaignIds->count();

        // Agents sous sa responsabilité (SUP et TC sur ses campagnes)
        $teamCount = Assignment::whereIn('campaign_id', $campaignIds)
            ->where('status', 'active')
            ->where('employee_id', '!=', $employee->id)
            ->count();

        // Liste des superviseurs sur ses campagnes
        $myTeams = Assignment::whereIn('campaign_id', $campaignIds)
            ->where('position_id', 2) // SUP
            ->where('status', 'active')
            ->with(['employee', 'campaign'])
            ->get()
            ->map(fn($assign) => [
                'id' => $assign->employee->id,
                'name' => $assign->employee->first_name . ' ' . $assign->employee->last_name,
                'initials' => strtoupper(substr($assign->employee->first_name, 0, 1) . substr($assign->employee->last_name, 0, 1)),
                'campaign' => $assign->campaign->name,
                'campaign_id' => $assign->campaign_id,
            ]);

        return Inertia::render('Dashboard/CPDashboard', [
            'stats' => [
                'teams_count' => $teamCount,
                'campaigns_count' => $campaignsCount,
                'pending_plannings' => 0, // À lier plus tard
            ],
            'my_teams' => $myTeams,
        ]);
    }
}
