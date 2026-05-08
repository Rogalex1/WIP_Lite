<?php

namespace App\Http\Controllers;

use App\Models\Reporting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\NewReportNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign; // 


class ReportingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
    {
        $user = Auth::user();
        $query = Reporting::with(['user', 'campaign']);

        // Filtrage selon le rôle
        if ($user->role === 'tc') {
            $query->where('user_id', $user->id);
        }

return Inertia::render('Reports/Index', [
        'reports' => $query->latest('report_date')->get(),
        // On filtre sur le statut 'active' défini dans ta migration
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
