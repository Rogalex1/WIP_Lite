<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanningModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'monday_hours' => $this->monday_hours,
            'tuesday_hours' => $this->tuesday_hours,
            'wednesday_hours' => $this->wednesday_hours,
            'thursday_hours' => $this->thursday_hours,
            'friday_hours' => $this->friday_hours,
            'saturday_hours' => $this->saturday_hours,
            'sunday_hours' => $this->sunday_hours,
            'total_hours' => $this->total_hours,
            'created_by' => $this->creator?->name,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}