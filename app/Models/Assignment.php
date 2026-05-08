<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
   protected $fillable = [
    'employee_id', 
    'manager_id', 
    'campaign_id', 
    'position_id', 
    'status',
    'start_date',  
    'end_date'
];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AssignmentHistory::class);
    }
}
