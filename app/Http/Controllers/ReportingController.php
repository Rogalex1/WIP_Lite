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

        // Filtrage hiérarchique selon le rôle
        if ($roleName === 'tc') {
            // Un TC ne voit que ses propres rapports
            $query->where('user_id', $user->id);
        } elseif ($roleName === 'sup') {
            // Un SUP voit ses rapports et ceux des agents qu'il supervise
            $employeeId = $user->employee->id ?? null;
            if ($employeeId) {
                $agentUserIds = Assignment::where('manager_id', $employeeId)
                    ->with('employee.user')
                    ->get()
                    ->pluck('employee.user_id')
                    ->filter()
                    ->push($user->id)
                    ->unique();
                $query->whereIn('user_id', $agentUserIds);
            } else {
                $query->where('user_id', $user->id);
            }
        } elseif ($roleName === 'cp') {
            // Un CP voit tout ce qui concerne ses superviseurs et leurs agents
            // Pour simplifier ici, on peut aussi dire qu'il voit tout s'il n'y a pas de lien CP->SUP strict dans Assignment
            // Mais si on suit la logique hiérarchique : CP voit tout ce que ses SUP voient.
            // Si on n'a pas de lien direct CP->SUP dans les assignments, on laisse l'Admin voir tout et le CP voir tout aussi pour l'instant
            // ou on peut filtrer par campagne s'il est affecté à des campagnes.
        }
        // Admin voit tout par défaut

        return Inertia::render('Reports/Index', [
            'reports' => $query->latest('report_date')->get(),
            'campaigns' => Campaign::where('status', 'active')->get(['id', 'name']),
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
