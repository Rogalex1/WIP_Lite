<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanningAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => [
                'id' => $this->employee->id,
                'name' => $this->employee->first_name . ' ' . $this->employee->last_name,
                'matricule' => $this->employee->matricule,
            ],
            'planning_model' => [
                'id' => $this->planningModel->id,
                'name' => $this->planningModel->name,
                'total_hours' => $this->planningModel->total_hours,
                'monday_hours' => $this->planningModel->monday_hours,
                'tuesday_hours' => $this->planningModel->tuesday_hours,
                'wednesday_hours' => $this->planningModel->wednesday_hours,
                'thursday_hours' => $this->planningModel->thursday_hours,
                'friday_hours' => $this->planningModel->friday_hours,
                'saturday_hours' => $this->planningModel->saturday_hours,
                'sunday_hours' => $this->planningModel->sunday_hours,
            ],
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'status' => $this->status,
            'validated_by' => $this->validator
                ? $this->validator->first_name . ' ' . $this->validator->last_name
                : null,
            'validated_at' => $this->validated_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}