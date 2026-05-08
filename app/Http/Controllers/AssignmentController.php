<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentHistory;
use App\Models\Employee;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Sécurité : Vérifier si le rôle existe
        if (!$user->role) {
            abort(403, 'Rôle utilisateur non défini');
        }

        $userRole = $user->role->name;
        $currentEmployee = Employee::where('user_id', $user->id)->first();

        // Accès restreint si pas admin et pas d'entrée employé
        if (!$currentEmployee && $userRole !== 'admin') {
            abort(403, 'Profil employé introuvable');
        }

        $campaignsTree = [];
        $availableEmployees = [];
        $campaigns = [];

        // Utilisation du match pour dispatcher la récupération des données
        match($userRole) {
            'admin' => $this->getAdminData($campaignsTree, $availableEmployees, $campaigns),
            'cp'    => $this->getCpData($currentEmployee, $campaignsTree, $availableEmployees, $campaigns),
            'sup'   => $this->getSupData($currentEmployee, $campaignsTree, $availableEmployees, $campaigns),
            'tc'    => $this->getTcData($currentEmployee, $campaignsTree, $availableEmployees, $campaigns),
            default => abort(403, 'Rôle non reconnu'),
        };

        $view = match($userRole) {
            'admin' => 'Assignments/Index',
            'cp'    => 'Assignments/CPIndex',
            'sup'   => 'Assignments/SUPIndex',
            'tc'    => 'Assignments/Index', // On peut créer un TCIndex plus tard si besoin
            default => 'Assignments/Index',
        };

        return Inertia::render($view, [
            'campaignsTree'      => $campaignsTree,
            'availableEmployees' => $availableEmployees,
            'campaigns'          => $campaigns,
            'userRole'           => $userRole,
            'isAdmin'            => $userRole === 'admin'
        ]);
    }

    private function getAdminData(&$campaignsTree, &$availableEmployees, &$campaigns)
    {
        $campaignsTree = Campaign::whereHas('assignments', function($query) {
            $query->where('status', 'active');
        })->with(['assignments' => function($query) {
            $query->where('status', 'active')->with(['employee', 'manager']);
        }])->get();

        $availableEmployees = Employee::whereDoesntHave('assignments', function($query) {
            $query->where('status', 'active');
        })->get();

        $campaigns = Campaign::where('status', 'active')->get();
    }

    private function getCpData($currentEmployee, &$campaignsTree, &$availableEmployees, &$campaigns)
    {
        $cpCampaignIds = Assignment::where('employee_id', $currentEmployee->id)
            ->where('position_id', 1) // 1 = CP
            ->where('status', 'active')
            ->pluck('campaign_id')
            ->toArray();

        $campaignsTree = Campaign::whereIn('id', $cpCampaignIds)
            ->whereHas('assignments', function($query) {
                $query->where('status', 'active');
            })
            ->with(['assignments' => function($query) {
                $query->where('status', 'active')
                      ->with(['employee', 'manager']);
            }])->get();

        // CP ne peut affecter que des SUP (2) et des TC (3)
        $availableEmployees = Employee::whereIn('position_id', [2, 3])
            ->whereDoesntHave('assignments', function($query) {
                $query->where('status', 'active');
            })->get();

        $campaigns = Campaign::whereIn('id', $cpCampaignIds)
            ->where('status', 'active')
            ->get();
    }

    private function getSupData($currentEmployee, &$campaignsTree, &$availableEmployees, &$campaigns)
    {
        $supAssignment = Assignment::where('employee_id', $currentEmployee->id)
            ->where('position_id', 2) // 2 = SUP
            ->where('status', 'active')
            ->first();

        if ($supAssignment) {
            $campaignsTree = Campaign::where('id', $supAssignment->campaign_id)
                ->whereHas('assignments', function($query) {
                    $query->where('status', 'active');
                })
                ->with(['assignments' => function($query) use ($currentEmployee) {
                    $query->where('status', 'active')
                          ->where(function($subQuery) use ($currentEmployee) {
                              $subQuery->where('employee_id', $currentEmployee->id)
                                       ->orWhere('manager_id', $currentEmployee->id);
                          })
                          ->with(['employee', 'manager']);
                }])->get();

            $campaigns = Campaign::where('id', $supAssignment->campaign_id)->get();
        }

        // SUP ne peut voir que des TC (3)
        $availableEmployees = Employee::where('position_id', 3)
            ->whereDoesntHave('assignments', function($query) {
                $query->where('status', 'active');
            })->get();
    }

    private function getTcData($currentEmployee, &$campaignsTree, &$availableEmployees, &$campaigns)
    {
        $tcAssignment = Assignment::where('employee_id', $currentEmployee->id)
            ->where('status', 'active')
            ->first();

        if ($tcAssignment) {
            $campaignsTree = Campaign::where('id', $tcAssignment->campaign_id)
                ->with(['assignments' => function($query) use ($currentEmployee) {
                    $query->where('status', 'active')
                          ->where('employee_id', $currentEmployee->id)
                          ->with(['employee', 'manager']);
                }])->get();
        }

        $availableEmployees = [];
        $campaigns = [];
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!in_array($user->role->name, ['admin', 'cp'])) {
            abort(403, 'Action non autorisée');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'campaign_id' => 'required|exists:campaigns,id',
            'manager_id'  => 'nullable|exists:employees,id',
            'position_id' => 'required|integer',
            'reason'      => 'nullable|string'
        ]);

        if ($user->role->name === 'cp') {
            $currentEmployee = Employee::where('user_id', $user->id)->firstOrFail();
            $this->validateCpAssignment($currentEmployee, $validated);

            // Si le CP affecte un SUP, le CP est automatiquement le manager
            if ($validated['position_id'] == 2) {
                $validated['manager_id'] = $currentEmployee->id;
            }
        }

        DB::transaction(function () use ($validated) {
            // Désactiver les anciennes affectations actives pour cet employé (sécurité)
            Assignment::where('employee_id', $validated['employee_id'])
                ->where('status', 'active')
                ->update(['status' => 'completed', 'end_date' => now()]);

            $assignment = Assignment::create([
                'employee_id' => $validated['employee_id'],
                'campaign_id' => $validated['campaign_id'],
                'manager_id'  => $validated['manager_id'],
                'position_id' => $validated['position_id'],
                'status'      => 'active',
                'start_date'  => now()->toDateString(),
            ]);

            AssignmentHistory::create([
                'assignment_id'   => $assignment->id,
                'employee_id'     => $validated['employee_id'],
                'new_manager_id'  => $validated['manager_id'],
                'new_campaign_id' => $validated['campaign_id'],
                'action_type'     => 'assign',
                'changed_by'      => auth()->id(),
                'reason'          => $validated['reason'] ?? 'Nouvelle affectation',
            ]);

            Employee::where('id', $validated['employee_id'])->update(['status' => 'affecté']);
        });

        return redirect()->back()->with('success', 'Affectation réussie.');
    }

    private function validateCpAssignment($cpEmployee, $validated)
    {
        $hasAccess = Assignment::where('employee_id', $cpEmployee->id)
            ->where('campaign_id', $validated['campaign_id'])
            ->where('position_id', 1)
            ->where('status', 'active')
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Vous ne pouvez affecter que sur vos propres campagnes');
        }
    }

    public function release(Assignment $assignment)
    {
        $user = auth()->user();
        $currentEmployee = Employee::where('user_id', $user->id)->first();
        $userRole = $user->role->name;

        // Vérification des droits de libération
        $canRelease = match($userRole) {
            'admin' => true,
            'cp'    => $this->isCpResponsibleForAssignment($currentEmployee, $assignment),
            'sup'   => $this->isSupResponsibleForAssignment($currentEmployee, $assignment),
            default => false
        };

        if (!$canRelease) {
            abort(403, 'Vous n\'avez pas les droits pour libérer cette ressource');
        }

        DB::transaction(function () use ($assignment) {
            $this->terminateAssignmentRecursive($assignment, 'Désaffectation manuelle');
        });

        return redirect()->back()->with('success', 'Ressource libérée avec succès.');
    }

    private function terminateAssignmentRecursive($assignment, $reason)
    {
        $this->terminateAssignment($assignment, $reason);

        // Libération en cascade (CP -> SUP -> TC)
        $subordinates = Assignment::where('manager_id', $assignment->employee_id)
            ->where('campaign_id', $assignment->campaign_id)
            ->where('status', 'active')
            ->get();

        foreach ($subordinates as $sub) {
            $this->terminateAssignmentRecursive($sub, 'Désaffectation en cascade (Supérieur libéré)');
        }
    }

    private function terminateAssignment($assignment, $reason)
    {
        AssignmentHistory::create([
            'assignment_id' => $assignment->id,
            'employee_id'   => $assignment->employee_id,
            'action_type'   => 'release',
            'changed_by'    => auth()->id(),
            'reason'        => $reason
        ]);

        $assignment->update([
            'status'   => 'completed',
            'end_date' => now()
        ]);

        // Remettre l'employé en statut "non affecté"
        Employee::where('id', $assignment->employee_id)->update(['status' => 'non affecté']);
    }

    private function isCpResponsibleForAssignment($cpEmployee, $assignment)
    {
        if (!$cpEmployee) return false;

        $cpCampaignIds = Assignment::where('employee_id', $cpEmployee->id)
            ->where('position_id', 1)
            ->where('status', 'active')
            ->pluck('campaign_id')
            ->toArray();

        return in_array($assignment->campaign_id, $cpCampaignIds);
    }

    private function isSupResponsibleForAssignment($supEmployee, $assignment)
    {
        if (!$supEmployee) return false;
        return $assignment->position_id === 3 && $assignment->manager_id === $supEmployee->id;
    }
}