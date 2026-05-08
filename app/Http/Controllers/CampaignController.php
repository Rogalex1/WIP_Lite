<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index()
    {
        return Inertia::render('Campaigns/Index', [
            'campaigns' => Campaign::latest()->get()
        ]);
    }

    // Pas besoin de create() car on utilise un Dialog PrimeVue sur l'index

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,completed',
        ]);

        Campaign::create($validated);
        
        return redirect()->route('campaigns.index')->with('success', 'Campagne créée !');
    }

    public function show(Campaign $campaign)
    {
        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign
        ]);
    }

    // Pas besoin de edit() car on utilise un Dialog PrimeVue sur l'index

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:active,inactive,completed',
        ]);

        $campaign->update($validated);

        return redirect()->back()->with('success', 'Campagne mise à jour !');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->back()->with('message```

### Pourquoi la description ne venait pas ?
Dans', 'Campagne supprimée.');
    }
}