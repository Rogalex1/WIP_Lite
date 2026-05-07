<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Position;
use App\Notifications\EmployeeCreatedNotification;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Afficher la liste des employés avec recherche et filtres
     * Utilise withoutTrashed() pour exclure les employés supprimés (soft deleted)
     */
    public function index(Request $request)
    {
        // Récupérer uniquement les employés NON supprimés
        $query = Employee::withoutTrashed()->with(['position', 'user']);

        // Recherche par terme
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->byStatus($request->input('status'));
        }

        // Filtre par poste
        if ($request->filled('position_id')) {
            $query->byPosition($request->input('position_id'));
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(10);
        $positions = Position::all();

        // Déterminer quelle vue utiliser (avec ou sans dialogs)
        // Par défaut, utilise la vue avec dialogs (IndexDialog)
        $view = 'Employees/IndexDialog';

        return Inertia::render($view, [
            'employees' => $employees,
            'positions' => $positions,
            'filters' => $request->only(['search', 'status', 'position_id']),
        ]);
    }

    /**
     * Afficher le formulaire de création
     * Redirige vers la page Index avec un paramètre pour ouvrir le dialog
     */
    public function create()
    {
        // Rediriger vers la page Index avec un paramètre pour ouvrir le dialog de création
        return redirect()->route('employees.index')->with('openCreateDialog', true);
    }

    /**
     * Enregistrer un nouvel employé
     * Affiche un toast de succès après la création
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = $this->employeeService->create($request->validated());
        auth()->user()->notify(new EmployeeCreatedNotification($employee));
        try {
            $employee = $this->employeeService->create($request->validated());

            // Message de succès avec le nom de l'employé créé
            return redirect()->route('employees.index')
                ->with('success', "L'employé {$employee->first_name} {$employee->last_name} a été créé avec succès.");
        } catch (\Exception $e) {
            // Message d'erreur en cas d'échec
            return redirect()->back()
                ->withInput()
                ->with('error', "Erreur lors de la création de l'employé : {$e->getMessage()}");
        }
    }

    /**
     * Afficher le formulaire d'édition
     * Redirige vers la page Index avec un paramètre pour ouvrir le dialog
     */
    public function edit(Employee $employee)
    {
        // Rediriger vers la page Index avec un paramètre pour ouvrir le dialog de modification
        return redirect()->route('employees.index')->with('editEmployeeId', $employee->id);
    }

    /**
     * Mettre à jour un employé
     * Affiche un toast de succès après la mise à jour
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        try {
            $updatedEmployee = $this->employeeService->update($employee, $request->validated());

            // Message de succès avec le nom de l'employé mis à jour
            return redirect()->route('employees.index')
                ->with('success', "L'employé {$updatedEmployee->first_name} {$updatedEmployee->last_name} a été mis à jour avec succès.");
        } catch (\Exception $e) {
            // Message d'erreur en cas d'échec
            return redirect()->back()
                ->withInput()
                ->with('error', "Erreur lors de la mise à jour de l'employé : {$e->getMessage()}");
        }
    }

    /**
     * Afficher les détails d'un employé
     */
    public function show(Employee $employee)
    {
        return Inertia::render('Employees/Show', [
            'employee' => $employee->load(['position', 'user', 'assignments']),
        ]);
    }

    /**
     * Supprimer un employé (soft delete)
     * Affiche un toast de succès après la suppression
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        try {
            // Sauvegarder le nom avant la suppression
            $employeeName = "{$employee->first_name} {$employee->last_name}";
            
            $this->employeeService->delete($employee);

            // Message de succès avec le nom de l'employé supprimé
            return redirect()->route('employees.index')
                ->with('success', "L'employé {$employeeName} a été supprimé avec succès.");
        } catch (\Exception $e) {
            // Message d'erreur en cas d'échec
            return redirect()->back()
                ->with('error', "Erreur lors de la suppression de l'employé : {$e->getMessage()}");
        }
    }

    /**
     * Changer le statut d'un employé
     * Affiche un toast de succès après le changement de statut
     */
    public function changeStatus(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:actif,inactif,suspendu',
        ]);

        try {
            $newStatus = $request->input('status');
            $this->employeeService->changeStatus($employee, $newStatus);

            // Traduire le statut en français pour le message
            $statusLabels = [
                'actif' => 'Actif',
                'inactif' => 'Inactif',
                'suspendu' => 'Suspendu',
            ];

            // Message de succès avec le nom de l'employé et le nouveau statut
            return redirect()->route('employees.index')
                ->with('success', "Le statut de {$employee->first_name} {$employee->last_name} a été changé en {$statusLabels[$newStatus]}.");
        } catch (\Exception $e) {
            // Message d'erreur en cas d'échec
            return redirect()->back()
                ->with('error', "Erreur lors du changement de statut : {$e->getMessage()}");
        }
    }

    /**
     * Afficher l'historique d'un employé
     */
    public function history(Employee $employee)
    {
        return Inertia::render('Employees/History', [
            'employee' => $employee->load(['position', 'user']),
            'history' => $employee->assignments()
                ->with(['manager', 'project'])
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    /**
     * Afficher la liste des employés supprimés (soft deleted)
     * Utilise onlyTrashed() pour récupérer uniquement les employés supprimés
     */
    public function trash(Request $request)
    {
        // Récupérer uniquement les employés supprimés (soft deleted)
        $query = Employee::onlyTrashed()->with(['position', 'user']);

        // Recherche par terme
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Filtre par poste
        if ($request->filled('position_id')) {
            $query->byPosition($request->input('position_id'));
        }

        // Récupérer les employés supprimés avec pagination
        $employees = $query->orderBy('deleted_at', 'desc')->paginate(10);
        $positions = Position::all();

        return Inertia::render('Employees/Trash', [
            'employees' => $employees,
            'positions' => $positions,
            'filters' => $request->only(['search', 'position_id']),
        ]);
    }

    /**
     * Restaurer un employé supprimé (soft delete)
     * Utilise restore() pour annuler la suppression logique
     * 
     * Note : On doit récupérer l'employé avec withTrashed() car il est soft-deleted
     */
    public function restore(Request $request, $id): RedirectResponse
    {
        try {
            // Récupérer l'employé supprimé (soft-deleted)
            $employee = Employee::withTrashed()->findOrFail($id);

            // Vérifier que l'employé est bien supprimé
            if (!$employee->trashed()) {
                return redirect()->back()
                    ->with('warning', "Cet employé n'est pas supprimé.");
            }

            // Restaurer l'employé
            $employee->restore();

            // Message de succès avec le nom de l'employé restauré
            return redirect()->route('employees.trash')
                ->with('success', "L'employé {$employee->first_name} {$employee->last_name} a été restauré avec succès.");
        } catch (\Exception $e) {
            // Message d'erreur en cas d'échec
            return redirect()->back()
                ->with('error', "Erreur lors de la restauration de l'employé : {$e->getMessage()}");
        }
    }

    /**
     * Supprimer définitivement un employé (force delete)
     * Utilise forceDelete() pour supprimer complètement de la base de données
     * Cette action est irréversible !
     * 
     * Note : On doit récupérer l'employé avec withTrashed() car il est soft-deleted
     */
    public function forceDelete(Request $request, $id): RedirectResponse
    {
        try {
            // Récupérer l'employé supprimé (soft-deleted)
            $employee = Employee::withTrashed()->findOrFail($id);

            // Sauvegarder le nom avant la suppression définitive
            $employeeName = "{$employee->first_name} {$employee->last_name}";

            // Supprimer définitivement l'employé de la base de données
            $employee->forceDelete();

            // Message de succès avec le nom de l'employé supprimé définitivement
            return redirect()->route('employees.trash')
                ->with('success', "L'employé {$employeeName} a été supprimé définitivement.");
        } catch (\Exception $e) {
            // Message d'erreur en cas d'échec
            return redirect()->back()
                ->with('error', "Erreur lors de la suppression définitive de l'employé : {$e->getMessage()}");
        }
    }
}
