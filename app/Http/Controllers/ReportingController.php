<?php

namespace App\Http\Controllers;

use App\Models\Reporting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\NewReportNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Employee;
use App\Models\Assignment;

class ReportingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Reporting::with(['user.employee', 'campaign']);
        $roleName = $user->role->name;

        // 1. Filtrage hiérarchique des rapports affichés
        if ($roleName === 'tc') {
            $query->where('user_id', $user->id);
        } elseif ($roleName === 'sup') {
            $employeeId = $user->employee->id ?? null;
            if ($employeeId) {
                $subordinateUserIds = Assignment::where('manager_id', $employeeId)
                    ->join('employees', 'assignments.employee_id', '=', 'employees.id')
                    ->pluck('employees.user_id')
                    ->push($user->id)
                    ->unique();
                $query->whereIn('user_id', $subordinateUserIds);
            }
        } elseif ($roleName === 'cp') {
            $employeeId = $user->employee->id ?? null;
            if ($employeeId) {
                // SUPs gérés par le CP
                $managedEmployeeIds = Assignment::where('manager_id', $employeeId)
                    ->pluck('employee_id')
                    ->unique();
                
                // TCs gérés par ces SUPs
                $tcEmployeeIds = Assignment::whereIn('manager_id', $managedEmployeeIds)
                    ->pluck('employee_id')
                    ->unique();
                
                $allSubordinateEmployeeIds = $managedEmployeeIds->concat($tcEmployeeIds)->unique();
                
                $subordinateUserIds = Employee::whereIn('id', $allSubordinateEmployeeIds)
                    ->pluck('user_id')
                    ->push($user->id)
                    ->unique();
                
                $query->whereIn('user_id', $subordinateUserIds);
            }
        }

        // 2. Filtrage des campagnes disponibles pour la saisie
        $campaignsQuery = Campaign::where('status', 'active');
        
        if ($roleName !== 'admin') {
            $employeeId = $user->employee->id ?? null;
            if ($employeeId) {
                $assignedCampaignIds = Assignment::where('employee_id', $employeeId)
                    ->where('status', 'active')
                    ->pluck('campaign_id')
                    ->unique();
                
                $campaignsQuery->whereIn('id', $assignedCampaignIds);
            } else {
                // Si l'employé n'est pas trouvé ou n'a pas d'assignations, on ne lui montre aucune campagne
                $campaignsQuery->whereRaw('1 = 0');
            }
        }

        return Inertia::render('Reports/Index', [
            'reports' => $query->latest('report_date')->get(),
            'campaigns' => $campaignsQuery->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'report_date' => 'required|date',
            'calls_count' => 'required|integer|min:0',
            'success_count' => 'required|integer|min:0',
            'dmc' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
        ]);

        $report = Auth::user()->reportings()->create($validated);

        // Notification (Assurez-vous que la classe NewReportNotification existe)
        // Vous pourriez vouloir notifier le superviseur plutôt que l'utilisateur lui-même
        // Auth::user()->notify(new NewReportNotification($report));

        return redirect()->back();
    }

    public function update(Request $request, Reporting $reporting)
    {
        // Sécurité : Seul le propriétaire ou un admin peut modifier
        if (Auth::id() !== $reporting->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Action non autorisée');
        }

        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'report_date' => 'required|date',
            'calls_count' => 'required|integer|min:0',
            'success_count' => 'required|integer|min:0',
            'dmc' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
        ]);

        $reporting->update($validated);

        return redirect()->back();
    }

    public function destroy(Reporting $reporting)
    {
        // Sécurité : Seul le propriétaire ou un admin peut supprimer
        if (Auth::id() !== $reporting->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Action non autorisée');
        }

        $reporting->delete();

        return redirect()->back();
    }
    /**
     * Display the specified resource.
     */
    public function show(Reporting $reporting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reporting $reporting)
    {
        //
    }

}
