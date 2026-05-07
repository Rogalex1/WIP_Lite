<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    /**
     * Générer un matricule unique
     */
    public function generateMatricule(): string
    {
        $count = Employee::withTrashed()->count() + 1;
        return 'EMP-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Créer un nouvel employé
     */
    public function create(array $data): Employee
    {
        $data['matricule'] = $this->generateMatricule();

        return Employee::create($data);
    }

    /**
     * Mettre à jour un employé
     */
    public function update(Employee $employee, array $data): Employee
    {
        // Ne pas permettre la modification du matricule
        unset($data['matricule']);

        $employee->update($data);

        return $employee->fresh();
    }

    /**
     * Supprimer un employé (soft delete)
     */
    public function delete(Employee $employee): bool
    {
        return $employee->delete();
    }

    /**
     * Restaurer un employé (restore)
     */
    public function restore(int $employeeId): bool
    {
        $employee = Employee::withTrashed()->find($employeeId);
        
        if (!$employee) {
            return false;
        }

        return $employee->restore();
    }

    /**
     * Changer le statut d'un employé
     */
    public function changeStatus(Employee $employee, string $status): Employee
    {
        $employee->update(['status' => $status]);

        return $employee->fresh();
    }

    /**
     * Rechercher des employés
     */
    public function search(?string $term = null, ?string $status = null, ?int $positionId = null)
    {
        $query = Employee::with(['position', 'user']);

        // Appliquer la recherche
        if ($term) {
            $query->search($term);
        }

        // Appliquer le filtre de statut
        if ($status) {
            $query->byStatus($status);
        }

        // Appliquer le filtre de poste
        if ($positionId) {
            $query->byPosition($positionId);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Obtenir les statistiques des employés
     */
    public function getStatistics(): array
    {
        $total = Employee::count();
        $actifs = Employee::active()->count();
        $inactifs = Employee::inactive()->count();
        $suspendus = Employee::suspended()->count();

        return [
            'total' => $total,
            'actifs' => $actifs,
            'inactifs' => $inactifs,
            'suspendus' => $suspendus,
            'pourcentage_actifs' => $total > 0 ? round(($actifs / $total) * 100, 2) : 0,
            'pourcentage_inactifs' => $total > 0 ? round(($inactifs / $total) * 100, 2) : 0,
            'pourcentage_suspendus' => $total > 0 ? round(($suspendus / $total) * 100, 2) : 0,
        ];
    }
}
