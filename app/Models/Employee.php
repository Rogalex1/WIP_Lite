<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'matricule',
        'first_name',
        'last_name',
        'birth_date',
        'phone',
        'email',
        'address',
        'position_id',
        'salary_base',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'salary_base' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    /**
     * Accessor : Nom complet
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Scopes de recherche et filtres
     */
    
    // Scope pour la recherche par nom, prénom, matricule ou email
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('matricule', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    // Scope pour filtrer par statut
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope pour filtrer par poste
    public function scopeByPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    // Employés actifs
    public function scopeActive($query)
    {
        return $query->where('status', 'actif');
    }

    // Employés inactifs
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactif');
    }

    // Employés suspendus
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspendu');
    }

    /**
     * Relations
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function managedAssignments()
    {
        return $this->hasMany(Assignment::class, 'manager_id');
    }

    public function planningAssignments()
    {
        return $this->hasMany(PlanningAssignment::class);
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }
}