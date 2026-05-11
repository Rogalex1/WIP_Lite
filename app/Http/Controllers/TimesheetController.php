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
    $week = $request->get('week', now()->format('Y-\WW'));
    $formattedWeek = (strpos($week, 'W') === false) ? str_replace('-', '-W', $week) : $week;

    try {
        $startDate = Carbon::parse($formattedWeek)->startOfWeek();
    } catch (\Exception $e) {
        $startDate = Carbon::now()->startOfWeek();
    }
    $endDate = $startDate->copy()->endOfWeek();

    $role = $user->role->name;
    $query = Employee::query();

    // Logique de filtrage selon le rôle
if ($role !== 'admin') {

    $query->whereHas('assignments', function($q) use ($user) {
        $q->where('manager_id', $user->employee->id)
          ->where('status', 'active');
    });

    if ($role === 'sup') {
        $query->whereHas('position', fn($q) => $q->where('code', 'TC'));
    }

    if ($role === 'cp') {
        $query->whereHas('position', fn($q) => $q->where('code', 'SUP'));
    }
}

    // Récupération des subordonnés
    $subordinates = ($role === 'tc') ? collect() : $query->with(['position'])->get();

    // AJOUT DE SOI-MÊME : On récupère l'employé connecté
    $self = $user->employee->load('position');
    
    // On fusionne : Soi-même d'abord, puis les subordonnés
    $employees = collect([$self])->concat($subordinates);

    // Charger les timesheets et les heures de planning (votre logique existante)
    $employees->each(function($employee) use ($startDate, $endDate) {
        $timesheet = Timesheet::where('employee_id', $employee->id)
            ->where('period_start', $startDate->format('Y-m-d'))
            ->where('period_end', $endDate->format('Y-m-d'))
            ->with('entries')
            ->first();

        $employee->timesheet_for_period = $timesheet;
        
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
            foreach ($days as $index => $day) {
                $date = $startDate->copy()->addDays($index);
                $planningHours[$date->format('Y-m-d')] = $model->{$day . '_hours'};
            }
        }
        $employee->planning_hours = $planningHours;
    });

    return Inertia::render('Timesheets/Index', [
        'employees' => $employees,
        'auth_employee_id' => $user->employee->id, // On passe l'ID pour le comparer en Vue
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
        'role' => $role,
        'currentWeek' => $week,
    ]);
}
    
    public function show(Employee $employee, Request $request)
    {
        $user = Auth::user();
        $this->authorizeAccess($user, $employee);
        
        $week = $request->get('week', now()->format('Y-\WW'));
        $year = (int)substr($week, 0, 4);
        $weekNumber = (int)substr($week, 6); // Corrigé: commencer à l'index 6 pour sauter "Y-W"
        
        $startDate = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();
        
        // Charger l'employé avec sa position
        $employee->load('position');
        
        // Récupérer ou créer la timesheet pour cette période
        $timesheet = Timesheet::firstOrCreate([
            'employee_id' => $employee->id,
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => $endDate->format('Y-m-d'),
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
            foreach ($days as $index => $day) {
                $date = $startDate->copy()->addDays($index);
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
    
public function store(Request $request, Employee $employee = null)
{
    $user = Auth::user();
    
    $validated = $request->validate([
        'week' => 'required|string',
        'entries' => 'required|array',
        'action' => 'required|string|in:draft,submit',
        'employee_ids' => 'nullable|array',
        'employee_ids.*' => 'integer|exists:employees,id',
        'is_bulk_entry' => 'nullable|boolean',
    ]);
    
    $week = $validated['week'];
    $entries = $validated['entries'];
    $action = $validated['action'];
    $isBulkEntry = $validated['is_bulk_entry'] ?? false;
    
    if ($isBulkEntry && !empty($validated['employee_ids'])) {
        // Mode saisie groupée
        $employeeIds = $validated['employee_ids'];
        $successCount = 0;
        $errors = [];
        
        foreach ($employeeIds as $employeeId) {
            try {
                $emp = Employee::find($employeeId);
                $this->authorizeAccess($user, $emp);
                $this->processTimesheetEntry($emp, $week, $entries, $action, $user);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Erreur avec l'employé {$emp->first_name} {$emp->last_name}: " . $e->getMessage();
            }
        }
        
        $message = "$successCount feuille(s) de temps enregistrée(s) avec succès.";
        if (!empty($errors)) {
            $message .= " Erreurs: " . implode(', ', $errors);
        }
        
        return redirect()->route('timesheets.bulkEntry', ['ids' => implode(',', $employeeIds), 'week' => $week])
            ->with('success', $message);
    } else {
        // Mode normal
        if (!$employee) {
            abort(400, 'Employé non spécifié.');
        }
        
        $this->authorizeAccess($user, $employee);
        $this->processTimesheetEntry($employee, $week, $entries, $action, $user);
        
        return redirect()->back()->with('success', 'Feuille de temps mise à jour.');
    }
}

private function processTimesheetEntry($employee, $week, $entries, $action, $user)
{
    $year = (int)substr($week, 0, 4);
    $weekNumber = (int)substr($week, 6);
    
    $startDate = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
    $endDate = $startDate->copy()->endOfWeek();
    
    $timesheet = Timesheet::firstOrCreate([
        'employee_id' => $employee->id,
        'period_start' => $startDate->format('Y-m-d'),
        'period_end' => $endDate->format('Y-m-d'),
    ]);
    
    if ($timesheet->status === 'validé') {
        throw new \Exception('Cette feuille de temps est déjà validée.');
    }
    
    foreach ($entries as $date => $data) {
        $totalHours = 0;
        $plannedHours = isset($data['planned_hours']) ? (float)$data['planned_hours'] : 0;

        // 1. On vérifie si on a bien les deux heures
        if (!empty($data['check_in']) && !empty($data['check_out'])) {
            try {
                // Nettoyage de la chaîne (on ne garde que HH:mm même si on reçoit HH:mm:ss)
                $timeIn = substr($data['check_in'], 0, 5);
                $timeOut = substr($data['check_out'], 0, 5);

                $checkIn = Carbon::createFromFormat('H:i', $timeIn);
                $checkOut = Carbon::createFromFormat('H:i', $timeOut);

                // Gestion de la pause (s'assurer que c'est un entier)
                $breakMinutes = isset($data['break_duration']) ? (int)$data['break_duration'] : 0;

                // Calcul de la différence brute en minutes
                $diffInMinutes = $checkIn->diffInMinutes($checkOut, false); 
                
                // Si le résultat est négatif, c'est que le départ est le lendemain
                if ($diffInMinutes < 0) {
                    $diffInMinutes += 1440; // On ajoute 24h en minutes
                }

                // Total heures = (Minutes travaillées - pause) / 60
                $totalHours = ($diffInMinutes - $breakMinutes) / 60;

            } catch (\Exception $e) {
                // En cas d'erreur de format, on laisse à 0 pour éviter le crash
                $totalHours = 0;
            }
        }

        // 2. Calcul de l'écart (Overtime)
        $overtimeHours = $totalHours - $plannedHours;

        TimesheetEntry::updateOrCreate([
            'timesheet_id' => $timesheet->id,
            'date' => $date,
        ], [
            'check_in' => $data['check_in'] ?: null,
            'check_out' => $data['check_out'] ?: null,
            'break_duration' => $data['break_duration'] ?? 0,
            'total_hours' => round($totalHours, 2),
            'planned_hours' => $plannedHours,
            'overtime_hours' => round($overtimeHours, 2),
            'absence_type' => $data['absence_type'] ?: null,
            'comment' => $data['comment'] ?: null,
        ]);
    }
    
    $timesheet->status = ($action === 'submit') ? 'soumis' : 'brouillon';
    $timesheet->save();
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
           $employee->load('position');
            $canAccess = $employee->position && $employee->position->code === 'TC';
            if (!$canAccess) {
                abort(403, 'Accès non autorisé.');
            }
        } elseif ($role === 'cp') {
            // CP peut accéder aux SUPs
            $employee->load('position');
            $canAccess = $employee->position && $employee->position->code === 'SUP';
            
            if (!$canAccess) {
                abort(403, 'Accès non autorisé.');
            }
        }
    }
    
    // Opérations en masse sur les timesheets
public function bulkSubmit(Request $request)
{
    $user = Auth::user();
    dd($user);
    if ($user->role->name === 'tc') {
        abort(403, 'Action non autorisée.');
    }

    // ✅ sécurisation
    $employeeIds = $request->input('employee_ids')
        ?? $request->input('ids')
        ?? [];

    if (!is_array($employeeIds)) {

        $employeeIds = explode(',', $employeeIds);
    }

    $employeeIds = array_filter($employeeIds);
    dd($employeeIds);
    if (empty($employeeIds)) {
        return back()->with('error', 'Aucun employé sélectionné.');
    }

    $week = $request->input('week');

    if (!$week) {
        return back()->with('error', 'Semaine manquante.');
    }

    $successCount = 0;
    $errors = [];

    foreach ($employeeIds as $employeeId) {
        try {
            $timesheet = Timesheet::where('employee_id', $employeeId)
                ->where('period_start', $week)
                ->first();

            if (!$timesheet) {
                $timesheet = Timesheet::create([
                    'employee_id' => $employeeId,
                    'period_start' => $week,
                    'period_end' => date('Y-m-d', strtotime($week . ' +6 days')),
                    'status' => 'soumis',
                    'submitted_at' => now(),
                ]);
            } elseif ($timesheet->status === 'brouillon') {
                $timesheet->update([
                    'status' => 'soumis',
                    'submitted_at' => now(),
                ]);
            }

            $successCount++;

        } catch (\Exception $e) {
            $errors[] = "Erreur employé $employeeId : " . $e->getMessage();
        }
    }

    return redirect()
        ->route('timesheets.index', ['week' => $week])
        ->with('success', "$successCount timesheet(s) soumis(s)")
        ->with('errors', $errors);
}
    
    public function bulkValidate(Request $request)
    {
        $user = Auth::user();
        $employeeIds = $request->input('employee_ids');
        $week = $request->input('week');
        
        // Seul les CP peuvent valider
        if ($user->role->name !== 'cp') {
            abort(403, 'Action non autorisée.');
        }
        
        $successCount = 0;
        $errors = [];
        
        foreach ($employeeIds as $employeeId) {
            try {
                $timesheet = Timesheet::where('employee_id', $employeeId)
                    ->where('period_start', $week)
                    ->where('status', 'soumis')
                    ->first();
                    
                if (!$timesheet) {
                    $errors[] = "Aucune timesheet soumise trouvée pour l'employé ID $employeeId";
                    continue;
                }
                
                // Vérifier que le CP a le droit de valider cette timesheet
                $canValidate = Assignment::where('manager_id', $user->employee->id)
                    ->where('employee_id', $employeeId)
                    ->where('status', 'actif')
                    ->exists();
                    
                if (!$canValidate) {
                    $errors[] = "Vous n'êtes pas autorisé à valider la timesheet de l'employé ID $employeeId";
                    continue;
                }
                
                $timesheet->status = 'validé';
                $timesheet->validated_by = $user->employee->id;
                $timesheet->validated_at = now();
                $timesheet->save();
                
                // Créer l'historique
                TimesheetHistory::create([
                    'timesheet_id' => $timesheet->id,
                    'employee_id' => $employeeId,
                    'old_status' => 'soumis',
                    'new_status' => 'validé',
                    'changed_by' => $user->employee->id,
                    'reason' => 'Validation en masse',
                    'created_at' => now(),
                ]);
                
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Erreur avec l'employé ID $employeeId: " . $e->getMessage();
            }
        }
        
        return redirect()->route('timesheets.index', ['week' => $week])
            ->with('success', "$successCount timesheet(s) validé(s) avec succès.")
            ->with('errors', $errors);
    }
    
public function bulkEntry(Request $request)
{
    $user = Auth::user();

    if ($user->role->name === 'tc') {
        abort(403, 'Action non autorisée.');
    }

    $ids = $request->input('ids');
    $week = $request->input('week');

    if (!$ids || !$week) {
        abort(400, 'Paramètres manquants.');
    }

    $employeeIds = explode(',', $ids);

    // ✅ Conversion semaine → vraies dates
    $year = (int) substr($week, 0, 4);
    $weekNumber = (int) substr($week, 6);

    $startDate = \Carbon\Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
    $endDate = $startDate->copy()->endOfWeek();

    // ✅ Vérifier planning
    $planningAssignments = PlanningAssignment::whereIn('employee_id', $employeeIds)
        ->where('start_date', '<=', $endDate)
        ->where('end_date', '>=', $startDate)
        ->where('status', 'validé')
        ->with('planningModel')
        ->get();

    if ($planningAssignments->count() !== count($employeeIds)) {
        return redirect()->route('timesheets.index', ['week' => $week])
            ->with('error', 'Certains employés n\'ont pas de planning validé.');
    }

    $planningModelIds = $planningAssignments->pluck('planning_model_id')->unique();

    if ($planningModelIds->count() > 1) {
        return redirect()->route('timesheets.index', ['week' => $week])
            ->with('error', 'Les employés sélectionnés n\'ont pas le même planning.');
    }

    $planningAssignment = $planningAssignments->first();
    $planningModel = $planningAssignment->planningModel;

    // 🔥 AJOUT ICI : génération des heures
   $planningHours = [];

$current = $startDate->copy();

$days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

for ($i = 0; $i < 7; $i++) {

    $dayKey = $days[$i];

    $planningHours[$current->toDateString()] = $planningModel->{$dayKey . '_hours'} ?? 0;

    $current->addDay();
}

    $employees = Employee::whereIn('id', $employeeIds)->get();

    foreach ($employees as $employee) {
        $this->authorizeAccess($user, $employee);
    }

    // Mock data
    $mockEmployee = (object)[
        'id' => 0,
        'first_name' => 'Group',
        'last_name' => 'Entry',
        'matricule' => 'BULK',
        'position' => (object)['name' => 'Saisie Groupée'],
    ];

    $mockTimesheet = (object)[
        'id' => 0,
        'status' => 'brouillon',
        'entries' => [],
    ];

    return Inertia::render('Timesheets/Show', [
        'employee' => $mockEmployee,
        'timesheet' => $mockTimesheet,
        'entries' => [],
        'planningHours' => $planningHours, // ✅ FIX ICI
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
        'week' => $week,
        'employeeIds' => $employeeIds,
        'isBulkEntry' => true,
        'employees' => $employees,
        'planningAssignment' => $planningAssignment,
    ]);
}

public function bulkStore(Request $request)
{
    $validated = $request->validate([
        'week' => 'required|string',
        'entries' => 'required|array',
        'action' => 'required|string|in:draft,submit',
        'employee_ids' => 'required|array',
        'employee_ids.*' => 'integer|exists:employees,id',
    ]);

    foreach ($validated['employee_ids'] as $employeeId) {
        $employee = Employee::findOrFail($employeeId);
        $this->processTimesheetEntry(
            $employee,
            $validated['week'],
            $validated['entries'],
            $validated['action'],
            Auth::user()
        );
    }

    return redirect()
    ->route('timesheets.index', ['week' => $validated['week']])
    ->with('success', 'Bulk timesheets enregistrées avec succès');
}
}
