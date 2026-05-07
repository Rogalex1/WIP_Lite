<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'matricule' => $this->matricule,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name, // Utilise l'accessor du modèle
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'hire_date' => $this->hire_date?->format('Y-m-d'),
            'salary_base' => $this->salary_base,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'position' => [
                'id' => $this->position?->id,
                'name' => $this->position?->name,
                'code' => $this->position?->code,
            ],
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Obtenir le libellé du statut
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'suspendu' => 'Suspendu',
            default => ucfirst($this->status),
        };
    }

    /**
     * Obtenir la couleur du statut pour l'UI
     */
    private function getStatusColor(): string
    {
        return match($this->status) {
            'actif' => 'success',
            'inactif' => 'secondary',
            'suspendu' => 'danger',
            default => 'info',
        };
    }
}
