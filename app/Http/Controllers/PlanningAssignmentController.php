<?php

namespace App\Http\Controllers;

use App\Models\PlanningAssignment;
use App\Models\PlanningHistory;
use App\Models\Employee;
use App\Models\PlanningModel;
use App\Http\Requests\StorePlanningAssignmentRequest;
use App\Http\Requests\UpdatePlanningAssignmentRequest;
use App\Http\Resources\PlanningAssignmentResource;
use App\Http\Resources\PlanningHistoryResource;
use Inertia\Inertia;

class PlanningAssignmentController extends Controller
{
    /**
     * Détermine le préfixe de route selon le rôle de l'utilisateur
     */
    private function getRoutePrefix(): string
    {
        return auth()->user()->role?->name === 'admin' ? 'admin' : 'cp';
    }

    /**
     * Génère le nom de route complet avec le bon préfixe
     */
    private function getRouteName(string $action): string
    {
        return $this->getRoutePrefix() . '.planning-assignments.' . $action;
    }

    public function index()
    {
        $planningAssignments = PlanningAssignment::with([
            'employee',
            'planningModel',
            'validator'
        ])->latest()->paginate(10);

        return Inertia::render('Planning/Assignments/Index', [
            'assignments' => PlanningAssignmentResource::collection($planningAssignments),
        ]);
    }

    public function create()
    {
        return Inertia::render('Planning/Assignments/Create', [
            'employees' => Employee::select('id', 'first_name', 'last_name', 'matricule')
                ->where('status', 'actif')
                ->get(),
            'planningModels' => PlanningModel::select('id', 'name', 'total_hours')->get(),
        ]);
    }

    public function store(StorePlanningAssignmentRequest $request)
    {
        $conflict = PlanningAssignment::where('employee_id', $request->employee_id)
            ->whereIn('status', ['en attente', 'validé'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date ?? '9999-12-31'])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date ?? '9999-12-31'])
                    ->orWhereNull('end_date');
            })->exists();

        if ($conflict) {
            return back()->with('error', 'Cet employé a déjà un planning actif sur cette période.');
        }

        PlanningAssignment::create($request->validated());

        return redirect()
            ->route($this->getRouteName('index'))
            ->with('success', 'Affectation créée avec succès.');
    }

    public function show(PlanningAssignment $planningAssignment)
    {
        $planningAssignment->load(['employee', 'planningModel', 'validator']);

        return Inertia::render('Planning/Assignments/Show', [
            'assignment' => (new PlanningAssignmentResource($planningAssignment))->resolve(),
            'histories' => [],
        ]);
    }

    public function edit(PlanningAssignment $planningAssignment)
    {
        $planningAssignment->load(['employee', 'planningModel', 'validator']);

        return Inertia::render('Planning/Assignments/Edit', [
            'assignment' => (new PlanningAssignmentResource($planningAssignment))->resolve(),
            'employees' => Employee::select('id', 'first_name', 'last_name', 'matricule')->where('status', 'actif')->get(),
            'planningModels' => PlanningModel::select('id', 'name', 'total_hours')->get(),
        ]);
    }

    public function history(PlanningAssignment $planningAssignment)
    {
        $planningAssignment->load(['employee', 'planningModel', 'validator']);

        $histories = $planningAssignment->histories()->with('user')->latest()->get();

        return Inertia::render('Planning/Assignments/History', [
            'assignment' => (new PlanningAssignmentResource($planningAssignment))->resolve(),
            'histories' => PlanningHistoryResource::collection($histories)->resolve(),
        ]);
    }

    public function update(UpdatePlanningAssignmentRequest $request, PlanningAssignment $planningAssignment)
    {
        if ($planningAssignment->status === 'validé') {
            return back()->with('error', 'Impossible de modifier une affectation déjà validée.');
        }

        $planningAssignment->update($request->validated());

        return redirect()
            ->route($this->getRouteName('index'))
            ->with('success', 'Affectation mise à jour avec succès.');
    }

    public function destroy(PlanningAssignment $planningAssignment)
    {
        // Logique métier : seul l'admin peut supprimer des affectations
        if (auth()->user()->role?->name === 'cp') {
            return back()->with('error', 'Le CP ne peut pas supprimer d\'affectations. Utilisez "Suspendre" ou "Terminer".');
        }

        if (auth()->user()->role?->name === 'sup') {
            return back()->with('error', 'Le SUP n\'a pas accès à la suppression d\'affectations.');
        }

        if (auth()->user()->role?->name === 'tc') {
            return back()->with('error', 'Le TC n\'a pas accès à la suppression d\'affectations.');
        }

        if ($planningAssignment->status === 'validé') {
            return back()->with('error', 'Impossible de supprimer une affectation validée.');
        }

        $planningAssignment->delete();

        return redirect()
            ->route($this->getRouteName('index'))
            ->with('success', 'Affectation supprimée avec succès.');
    }

    public function validateAssignment(PlanningAssignment $planningAssignment)
    {
        // Logique métier : seul le CP peut valider (l'admin peut aussi en god mode)
        $userRole = auth()->user()->role?->name;
        if (!in_array($userRole, ['admin', 'cp'])) {
            return back()->with('error', 'Seul le CP peut valider les affectations.');
        }

        if ($planningAssignment->status !== 'en attente') {
            return back()->with('error', 'Seules les affectations en attente peuvent être validées.');
        }

        $oldStatus = $planningAssignment->status;

        $planningAssignment->update([
            'status' => 'validé',
            'validated_by' => auth()->user()->employee->id,
            'validated_at' => now(),
        ]);

        PlanningHistory::create([
            'planning_assignment_id' => $planningAssignment->id,
            'old_status' => $oldStatus,
            'new_status' => 'validé',
            'changed_by' => auth()->user()->id,
            'reason' => request('reason') ?? "Validation de l'affectation",
        ]);

        return back()->with('success', 'Affectation validée avec succès.');
    }

    public function suspend(PlanningAssignment $planningAssignment)
    {
        // Logique métier : seul le CP peut suspendre (l'admin peut aussi en god mode)
        $userRole = auth()->user()->role?->name;
        if (!in_array($userRole, ['admin', 'cp'])) {
            return back()->with('error', 'Seul le CP peut suspendre les affectations.');
        }

        if ($planningAssignment->status !== 'validé') {
            return back()->with('error', 'Seules les affectations validées peuvent être suspendues.');
        }

        $oldStatus = $planningAssignment->status;

        $planningAssignment->update(['status' => 'suspendu']);

        PlanningHistory::create([
            'planning_assignment_id' => $planningAssignment->id,
            'old_status' => $oldStatus,
            'new_status' => 'suspendu',
            'changed_by' => auth()->user()->id,
            'reason' => request('reason') ?? "Suspension de l'affectation",
        ]);

        return back()->with('success', 'Affectation suspendue avec succès.');
    }

    public function terminate(PlanningAssignment $planningAssignment)
    {
        // Logique métier : seul le CP peut terminer (l'admin peut aussi en god mode)
        $userRole = auth()->user()->role?->name;
        if (!in_array($userRole, ['admin', 'cp'])) {
            return back()->with('error', 'Seul le CP peut terminer les affectations.');
        }

        if ($planningAssignment->status === 'terminé') {
            return back()->with('error', 'Cette affectation est déjà terminée.');
        }

        $oldStatus = $planningAssignment->status;

        $planningAssignment->update([
            'status' => 'terminé',
            'end_date' => now()->toDateString(),
        ]);

        PlanningHistory::create([
            'planning_assignment_id' => $planningAssignment->id,
            'old_status' => $oldStatus,
            'new_status' => 'terminé',
            'changed_by' => auth()->user()->id,
            'reason' => request('reason') ?? "Fin de l'affectation",
        ]);

        return back()->with('success', 'Affectation terminée avec succès.');
    }

    public function indexSup()
    {
        $employee = auth()->user()->employee;

        $assignments = PlanningAssignment::with(['employee', 'planningModel', 'validator'])
            ->whereHas('employee', function ($query) use ($employee) {
                $query->whereHas('assignments', function ($q) use ($employee) {
                    $q->where('manager_id', $employee->id);
                });
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Planning/Assignments/Index', [
            'assignments' => PlanningAssignmentResource::collection($assignments),
        ]);
    }   
}