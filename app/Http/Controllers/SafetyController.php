<?php

namespace App\Http\Controllers;

use App\Models\ClientLocation;
use App\Models\SafetyIncident;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SafetyController extends Controller
{
    public function index()
    {
        $incidents = SafetyIncident::with(['location', 'ticket', 'reporter'])->latest('incident_date')->paginate(15);
        $locations = ClientLocation::orderBy('name')->get();
        $tickets = Ticket::latest()->take(30)->get();

        return view('safety.index', compact('incidents', 'locations', 'tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'incident_type' => 'required|string|max:100',
            'severity' => 'required|in:Low,Medium,High,Critical',
            'client_location_id' => 'nullable|exists:client_locations,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'incident_date' => 'required|date',
            'description' => 'required|string',
            'corrective_action' => 'nullable|string',
        ]);

        $validated['reported_by'] = Auth::id();
        $validated['status'] = 'Open';

        SafetyIncident::create($validated);

        return back()->with('status', 'Safety incident / near-miss logged.');
    }

    public function updateStatus(Request $request, SafetyIncident $incident)
    {
        $validated = $request->validate([
            'status' => 'required|in:Open,Investigated,Closed',
            'corrective_action' => 'nullable|string',
        ]);

        $incident->update($validated);

        return back()->with('status', 'Safety incident updated.');
    }
}
