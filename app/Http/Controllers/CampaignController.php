<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->role) {
            abort(403, 'Rôle utilisateur non défini');
        }

        $userRole = $user->role->name;
        $currentEmployee = Employee::where('user_id', $user->id)->first();

        // Accès restreint si pas admin et pas d'entrée employé
        if (!$currentEmployee && $userRole !== 'admin') {
            abort(403, 'Profil employé introuvable');
        }

        $campaigns = match($userRole) {
            'admin' => Campaign::latest()->get(),
            'cp' => $this->getCpCampaigns($currentEmployee),
            'sup' => $this->getSupCampaigns($currentEmployee),
            'tc' => $this->getTcCampaigns($currentEmployee),
            default => collect([])
        };

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
            'userRole' => $userRole,
            'isAdmin' => $userRole === 'admin'
        ]);
    }

    /**
     * Récupère les campagnes du Chef de Plateau
     */
    private function getCpCampaigns($employee)
    {
        return Campaign::whereHas('assignments', function($query) use ($employee) {
            $query->where('employee_id', $employee->id)
                  ->where('position_id', 1)
                  ->where('status', 'active');
        })->latest()->get();
    }

    /**
     * Récupère les campagnes du Superviseur
     */
    private function getSupCampaigns($employee)
    {
        return Campaign::whereHas('assignments', function($query) use ($employee) {
            $query->where('employee_id', $employee->id)
                  ->where('position_id', 2)
                  ->where('status', 'active');
        })->latest()->get();
    }

    /**
     * Récupère les campagnes du Téléconseiller
     */
    private function getTcCampaigns($employee)
    {
        return Campaign::whereHas('assignments', function($query) use ($employee) {
            $query->where('employee_id', $employee->id)
                  ->where('position_id', 3)
                  ->where('status', 'active');
        })->latest()->get();
    }

    public function store(Request $request)
    {
        // Seul l'admin peut créer
        if (auth()->user()->role->name !== 'admin') {
            abort(403, 'Non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,completed',
        ]);

        Campaign::create($validated);
        
        return redirect()->route('campaigns.index')->with('success', 'Campagne créée !');
    }

    public function show(Campaign $campaign)
    {
        $user = auth()->user();
        $userRole = $user->role->name;

        if ($userRole !== 'admin') {
            $employee = Employee::where('user_id', $user->id)->firstOrFail();
            $hasAccess = Assignment::where('employee_id', $employee->id)
                ->where('campaign_id', $campaign->id)
                ->where('status', 'active')
                ->exists();

            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette campagne');
            }
        }

        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign->load(['assignments' => function($query) {
                $query->where('status', 'active')->with(['employee', 'manager']);
            }])
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        // Seul l'admin peut mettre à jour
        if (auth()->user()->role->name !== 'admin') {
            abort(403, 'Non autorisé');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:active,inactive,completed',
        ]);

        $campaign->update($validated);

        return redirect()->back()->with('success', 'Campagne mise à jour !');
    }

    public function destroy(Campaign $campaign)
    {
        // Seul l'admin peut supprimer
        if (auth()->user()->role->name !== 'admin') {
            abort(403, 'Non autorisé');
        }

        $campaign->delete();
        return redirect()->back()->with('success', 'Campagne supprimée.');
    }
}