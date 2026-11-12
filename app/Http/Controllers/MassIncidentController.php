<?php

namespace App\Http\Controllers;

use App\Models\ClientLocation;
use App\Models\MassIncident;
use App\Models\Ticket;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MassIncidentController extends Controller
{
    public function index()
    {
        $incidents = MassIncident::with(['creator', 'tickets.location'])->latest()->paginate(10);
        $locations = ClientLocation::orderBy('name')->get();

        return view('incidents.index', compact('incidents', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'incident_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'priority' => 'required|in:P1,P2,P3,P4',
            'category' => 'required|string|max:100',
            'location_ids' => 'required|array|min:1',
            'location_ids.*' => 'exists:client_locations,id',
        ]);

        $createdCount = 0;

        DB::transaction(function () use ($validated, &$createdCount) {
            $nextIncNum = (MassIncident::max('id') ?? 0) + 1;
            $incident = MassIncident::create([
                'incident_code' => 'INC-' . date('Y') . '-' . str_pad($nextIncNum, 3, '0', STR_PAD_LEFT),
                'title' => $validated['title'],
                'incident_type' => $validated['incident_type'],
                'description' => $validated['description'] ?? null,
                'occurred_at' => now(),
                'created_by' => Auth::id(),
                'status' => 'Active',
            ]);

            $maxTicketId = Ticket::withTrashed()->max('id') ?? 0;

            foreach ($validated['location_ids'] as $locId) {
                do {
                    $maxTicketId++;
                    $ticketNumber = 'SC-NAG-' . str_pad($maxTicketId, 4, '0', STR_PAD_LEFT);
                } while (Ticket::withTrashed()->where('ticket_number', $ticketNumber)->exists());
                $ticket = Ticket::create([
                    'ticket_number' => $ticketNumber,
                    'client_location_id' => $locId,
                    'mass_incident_id' => $incident->id,
                    'reported_by_user_id' => Auth::id(),
                    'priority' => $validated['priority'],
                    'status' => 'Open',
                    'category' => $validated['category'],
                    'billing_type' => 'AMC Covered',
                    'problem_title' => "[{$incident->incident_code}] {$validated['title']}",
                    'problem_description' => $validated['description'] ?? "Bulk incident ticket raised under {$incident->incident_code}.",
                    'eta_display' => 'Today',
                ]);
                NotificationService::sendTicketAlert(
                    $ticket,
                    'ticket_created',
                    "Bulk incident ticket {$ticketNumber} created under {$incident->incident_code}."
                );
                $createdCount++;
            }
        });

        return back()->with('status', "Mass incident logged and {$createdCount} branch tickets created.");
    }
}
