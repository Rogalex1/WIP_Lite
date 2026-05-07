<?php

namespace App\Http\Controllers;

use App\Models\Reporting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\NewReportNotification;


class ReportingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $query = Reporting::with(['user', 'campaign']);

    // Si c'est un TC, il ne voit que ses propres chiffres
    if (auth()->user()->role === 'tc') {
        $query->where('user_id', auth()->id());
    }

    return Inertia::render('Reports/Index', [
        'reports' => $query->latest('report_date')->get(),
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

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

    $report = auth()->user()->reportings()->create($validated);

    // Notifier le Superviseur ou l'Admin (exemple : l'utilisateur connecté)
    auth()->user()->notify(new NewReportNotification($report));

    return redirect()->back()->with('message', 'Rapport enregistré avec succès');
}

public function update(Request $request, Reporting $reporting)
{
    $reporting->update($request->all());
    return redirect()->back()->with('message', 'Rapport mis à jour');
}

public function destroy(Reporting $reporting)
{
    $reporting->delete();
    return redirect()->back()->with('message', 'Rapport supprimé');
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
