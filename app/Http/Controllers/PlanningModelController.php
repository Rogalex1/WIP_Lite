<?php

namespace App\Http\Controllers;

use App\Models\PlanningModel;
use App\Http\Requests\StorePlanningModelRequest;
use App\Http\Requests\UpdatePlanningModelRequest;
use App\Http\Resources\PlanningModelResource;
use Inertia\Inertia;

class PlanningModelController extends Controller
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
        return $this->getRoutePrefix() . '.planning-models.' . $action;
    }

    public function index()
    {
       
        $models = PlanningModel::with('creator')
            ->latest()
            ->paginate(10);

        return Inertia::render('Planning/Models/Index', [
            'models' => PlanningModelResource::collection($models),
        ]);
    }

    public function create()
    {
        return Inertia::render('Planning/Models/Create');
    }

    public function store(StorePlanningModelRequest $request)
    {
        PlanningModel::create([
            ...$request->validated(),
            'created_by' => auth()->user()->id,
        ]);

        return redirect()
            ->route($this->getRouteName('index'))
            ->with('success', 'Modèle de planning créé avec succès.');
    }

    public function show(PlanningModel $planningModel)
    {
        $planningModel->load('creator', 'assignments.employee');

        return Inertia::render('Planning/Models/Show', [
            'model' => (new PlanningModelResource($planningModel))->resolve(), // ✅
        ]);
    }

    public function edit(PlanningModel $planningModel)
    {
        return Inertia::render('Planning/Models/Edit', [
            'model' => (new PlanningModelResource($planningModel))->resolve(), // ✅
        ]);
    }

    public function update(UpdatePlanningModelRequest $request, PlanningModel $planningModel)
    {
        $planningModel->update($request->validated());

        return redirect()
            ->route($this->getRouteName('index'))
            ->with('success', 'Modèle de planning mis à jour avec succès.');
    }

    public function destroy(PlanningModel $planningModel)
    {
        $hasActiveAssignments = $planningModel->assignments()
            ->whereIn('status', ['en attente', 'validé'])
            ->exists();

        if ($hasActiveAssignments) {
            return back()->with('error', 'Impossible de supprimer ce modèle, il est utilisé dans des affectations actives.');
        }

        $planningModel->delete();

        return redirect()
            ->route($this->getRouteName('index'))
            ->with('success', 'Modèle de planning supprimé avec succès.');
    }
}