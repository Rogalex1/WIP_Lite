<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Models\TimesheetHistory;
use App\Models\PlanningAssignment;
use App\Models\PlanningModel;
use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TimesheetController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();

    // 1. On récupère la semaine. Si vide, on prend la semaine actuelle au format ISO (ex: 2024-W18)
    $week = $request->get('week', now()->format('Y-\WW'));

    // Sécurité : Si le format reçu est "2024-18" au lieu de "2024-W18", on rajoute le W
    $formattedWeek = (strpos($week, 'W') === false) 
        ? str_replace('-', '-W', $week) 
        : $week;

    // 2. Calcul précis du début et de la fin de semaine
    try {
        // Carbon::parse sur un format ISO (YYYY-Www) donne toujours le Lundi
        $startDate = Carbon::parse($formattedWeek)->startOfWeek();
    } catch (\Exception $e) {
        $startDate = Carbon::now()->startOfWeek();
    }
    
    $endDate = $startDate->copy()->endOfWeek();

    // Logique d'accès selon le rôle
    $employees = [];
    $role = $user->role->name;

    if ($role === 'sup') {
        $employees = Employee::whereHas('assignments', function($query) use ($user) {
            $query->where('manager_id', $user->employee->id)
                  ->where('status', 'actif');
        })->whereHas('position', function($query) {
            $query->where('code', 'TC');
        })->with(['position'])->get(); // On retire timesheets.entries d'ici pour le charger proprement après

    } elseif ($role === 'cp') {
        $employees = Employee::whereHas('assignments', function($query) use ($user) {
            $query->where('manager_id', $user->employee->id)
                  ->where('status', 'actif');
        })->whereHas('position', function($query) {
            $query->where('code', 'SUP');
        })->with(['position'])->get();

    } elseif ($role === 'tc') {
        $employees = collect([$user->employee])->map(function($employee) {
            $employee->load(['position']);
            return $employee;
        });
    }

    // 3. Charger les timesheets et les heures de planning pour chaque employé
    $employees->each(function($employee) use ($startDate, $endDate) {
        // Charger la timesheet pour la période
        $timesheet = Timesheet::where('employee_id', $employee->id)
            ->where('period_start', $startDate->format('Y-m-d'))
            ->where('period_end', $endDate->format('Y-m-d'))
            ->with('entries')
            ->first();

        $employee->timesheet_for_period = $timesheet;
        
        // Charger les heures de planning de référence
        $planningAssignment = PlanningAssignment::where('employee_id', $employee->id)
            ->where('start_date', '<=', $startDate)
            ->where('end_date', '>=', $endDate)
            ->where('status', 'validé')
            ->with('planningModel')
            ->first();
            
        $planningHours = [];
        if ($planningAssignment) {
            $model = $planningAssignment->planningModel;
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                $date = $startDate->copy()->startOfWeek()->modify($day);
                $planningHours[$date->format('Y-m-d')] = $model->{$day . '_hours'};
            }
        }
        
        $employee->planning_hours = $planningHours;
    });

    return Inertia::render('Timesheets/Index', [
        'employees' => $employees,
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
        'role' => $role,
        'currentWeek' => $week, // On renvoie la semaine d'origine pour le sélecteur
    ]);
}
    
    public function show(Employee $employee, Request $request)
    {
        $user = Auth::user();
        $this->authorizeAccess($user, $employee);
        
        $week = $request->get('week', now()->format('Y-\WW'));
        $year = substr($week, 0, 4);
        $weekNumber = substr($week, 5);
        
        $startDate = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();
        
        // Charger l'employé avec sa position
        $employee->load('position');
        
        // Récupérer ou créer la timesheet pour cette période
        $timesheet = Timesheet::firstOrCreate([
            'employee_id' => $employee->id,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ], [
            'status' => 'brouillon',
        ]);
        
        // Récupérer les entrées existantes
        $entries = TimesheetEntry::where('timesheet_id', $timesheet->id)
            ->get()
            ->keyBy('date');
        
        // Récupérer le planning de référence
        $planningAssignment = PlanningAssignment::where('employee_id', $employee->id)
            ->where('start_date', '<=', $startDate)
            ->where('end_date', '>=', $endDate)
            ->where('status', 'validé')
            ->with('planningModel')
            ->first();
        
        $planningHours = [];
        if ($planningAssignment) {
            $model = $planningAssignment->planningModel;
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                $date = $startDate->copy()->startOfWeek()->modify($day);
                $planningHours[$date->format('Y-m-d')] = $model->{$day . '_hours'};
            }
        }
        
        return Inertia::render('Timesheets/Show', [
            'employee' => $employee,
            'timesheet' => $timesheet,
            'entries' => $entries,
            'planningHours' => $planningHours,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'planningAssignment' => $planningAssignment,
            'week' => $week,
        ]);
    }
    
    public function store(Request $request, Employee $employee)
    {
        $user = Auth::user();
        $this->authorizeAccess($user, $employee);
        
        $week = $request->get('week');
        $year = (int) substr($week, 0, 4);
        $weekNumber = (int) substr($week, 5);
        
        $startDate = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();
        
        $timesheet = Timesheet::firstOrCreate([
            'employee_id' => $employee->id,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ]);
        
        // Vérifier que la timesheet n'est pas déjà validée
        if ($timesheet->status === 'validé') {
            return back()->with('error', 'Cette feuille de temps est déjà validée et ne peut plus être modifiée.');
        }
        
        $entries = $request->get('entries', []);
        
        foreach ($entries as $date => $data) {
            // Calcul des heures
            $totalHours = 0;
            $overtimeHours = 0;
            
            if ($data['check_in'] && $data['check_out']) {
                $checkIn = Carbon::parse($data['check_in']);
                $checkOut = Carbon::parse($data['check_out']);
                $breakMinutes = $data['break_duration'] ?? 0;
                
                $totalHours = $checkOut->diffInMinutes($checkIn) / 60 - ($breakMinutes / 60);
                
                // Calcul des heures supplémentaires
                $plannedHours = $data['planned_hours'] ?? 0;
                $overtimeHours = max(0, $totalHours - $plannedHours);
            }
            
            // Créer ou mettre à jour l'entrée
            TimesheetEntry::updateOrCreate([
                'timesheet_id' => $timesheet->id,
                'date' => $date,
            ], [
                'check_in' => $data['check_in'] ?? null,
                'check_out' => $data['check_out'] ?? null,
                'break_duration' => $data['break_duration'] ?? 0,
                'total_hours' => $totalHours,
                'planned_hours' => $data['planned_hours'] ?? 0,
                'overtime_hours' => $overtimeHours,
                'absence_type' => $data['absence_type'] ?? null,
                'comment' => $data['comment'] ?? null,
            ]);
        }
        
        // Mettre à jour le statut
        $action = $request->get('action');
        if ($action === 'submit') {
            $timesheet->status = 'soumis';
        } else {
            $timesheet->status = 'brouillon';
        }
        
        $timesheet->save();
        
        return redirect()->back()->with('success', 'Feuille de temps enregistrée avec succès.');
    }
    
    public function validate(Request $request, Timesheet $timesheet)
    {
        $user = Auth::user();
        
        // Seul un CP peut valider
        if ($user->role->name !== 'cp') {
            abort(403, 'Action non autorisée.');
        }
        
        // Vérifier que le CP a le droit de valider cette timesheet
        $canValidate = Assignment::where('manager_id', $user->employee->id)
            ->where('employee_id', $timesheet->employee_id)
            ->where('status', 'actif')
            ->exists();
            
        if (!$canValidate) {
            abort(403, 'Vous n\'êtes pas autorisé à valider cette feuille de temps.');
        }
        
        $timesheet->status = 'validé';
        $timesheet->validated_by = $user->employee->id;
        $timesheet->validated_at = now();
        $timesheet->save();
        
        // Créer l'historique
        TimesheetHistory::create([
            'timesheet_id' => $timesheet->id,
            'employee_id' => $timesheet->employee_id,
            'old_status' => 'soumis',
            'new_status' => 'validé',
            'changed_by' => $user->employee->id,
            'reason' => 'Validation par le Chef de Plateau',
            'created_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Feuille de temps validée avec succès.');
    }
    
    private function authorizeAccess($user, $employee)
    {
        $role = $user->role->name;
        
        if ($role === 'tc') {
            // TC ne peut voir que sa propre page
            if ($user->employee->id !== $employee->id) {
                abort(403, 'Accès non autorisé.');
            }
        } elseif ($role === 'sup') {
            // SUP peut voir ses TC
            $canAccess = Assignment::where('manager_id', $user->employee->id)
                ->where('employee_id', $employee->id)
                ->where('status', 'actif')
                ->exists();
                
            if (!$canAccess) {
                abort(403, 'Accès non autorisé.');
            }
        } elseif ($role === 'cp') {
            // CP peut voir ses superviseurs
            $canAccess = Assignment::where('manager_id', $user->employee->id)
                ->where('employee_id', $employee->id)
                ->where('status', 'actif')
                ->exists();
                
            if (!$canAccess) {
                abort(403, 'Accès non autorisé.');
            }
        }
    }
}
