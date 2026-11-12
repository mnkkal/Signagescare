<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ClientLocation;
use App\Models\SafetyChecklist;
use App\Models\Setting;
use App\Models\SignageAsset;
use App\Models\SparePart;
use App\Models\Ticket;
use App\Models\TicketEvidence;
use App\Models\TicketPart;
use App\Models\TicketVisit;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['location', 'asset', 'technician', 'reportedBy']);

        // RBAC Data Scoping (#37): Store Managers only see their own client's locations
        if ($user && $user->hasRole('Store Manager') && $user->client_id) {
            $query->whereHas('location', fn ($q) => $q->where('client_id', $user->client_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('location_id')) {
            $query->where('client_location_id', $request->location_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('problem_title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('location', fn ($l) => $l->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('asset', fn ($a) => $a->where('asset_tag', 'like', "%{$search}%")->orWhere('asset_name', 'like', "%{$search}%"));
            });
        }

        $tickets = $query->latest()->paginate(15);
        $locationsQuery = ClientLocation::where('is_active', true);
        if ($user && $user->hasRole('Store Manager') && $user->client_id) {
            $locationsQuery->where('client_id', $user->client_id);
        }
        $locations = $locationsQuery->get();
        $technicians = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Technician', 'Subcontractor / Vendor']))->get();

        return view('tickets.index', compact('tickets', 'locations', 'technicians'));
    }

    public function create()
    {
        $user = Auth::user();
        $locationsQuery = ClientLocation::with('assets')->where('is_active', true);
        if ($user && $user->hasRole('Store Manager') && $user->client_id) {
            $locationsQuery->where('client_id', $user->client_id);
        }
        $locations = $locationsQuery->get();

        return view('tickets.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_location_id' => ['required', 'exists:client_locations,id'],
            'signage_asset_id' => ['nullable', 'exists:signage_assets,id'],
            'problem_title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'priority' => ['required', 'in:P1,P2,P3,P4'],
            'billing_type' => ['nullable', 'in:AMC Covered,Under Warranty,Chargeable'],
            'estimated_amount' => ['nullable', 'numeric', 'min:0'],
            'problem_description' => ['required', 'string'],
            'issue_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $location = ClientLocation::findOrFail($request->client_location_id);

        $cityCode = strtoupper(substr($location->city ?? 'NAG', 0, 3));
        $seedNum = (int) (Ticket::withTrashed()->max('id') ?? 0) + 100;
        do {
            $ticketNumber = sprintf('SC-%s-%04d', $cityCode, $seedNum++);
        } while (Ticket::withTrashed()->where('ticket_number', $ticketNumber)->exists());

        $slaMap = [
            'P1' => ['response' => 240, 'resolution' => 720],
            'P2' => ['response' => 480, 'resolution' => 1440],
            'P3' => ['response' => 720, 'resolution' => 2880],
            'P4' => ['response' => 1440, 'resolution' => 4320],
        ];
        $sla = $slaMap[$request->priority] ?? $slaMap['P3'];

        $threshold = (float) Setting::getValue('quotation_approval_threshold', 5000);
        $estimated = $request->filled('estimated_amount') ? (float) $request->estimated_amount : null;

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'client_location_id' => $request->client_location_id,
            'signage_asset_id' => $request->signage_asset_id,
            'reported_by_user_id' => auth()->id(),
            'priority' => $request->priority,
            'status' => 'Open',
            'category' => $request->category,
            'billing_type' => $request->input('billing_type', 'AMC Covered'),
            'estimated_amount' => $estimated,
            'requires_client_approval' => $estimated !== null && $estimated > $threshold,
            'response_sla_minutes' => $sla['response'],
            'resolution_sla_minutes' => $sla['resolution'],
            'problem_title' => $request->problem_title,
            'problem_description' => $request->problem_description,
            'eta_display' => 'Pending Review',
        ]);

        if ($request->hasFile('issue_image')) {
            $path = $request->file('issue_image')->store('evidence', 'public');
            TicketEvidence::create([
                'ticket_id' => $ticket->id,
                'evidence_type' => 'before',
                'file_path' => $path,
                'caption' => 'Initial issue report photo',
                'uploaded_by_user_id' => auth()->id(),
                'captured_at' => now(),
            ]);
        }

        NotificationService::sendTicketAlert(
            $ticket,
            'ticket_created',
            "New Signage Care ticket {$ticket->ticket_number} ({$ticket->priority}) created for {$location->name}: {$ticket->problem_title}."
        );

        AuditLog::log('ticket_created', "Service ticket {$ticket->ticket_number} created for {$location->name}.", $ticket);

        return redirect()->route('tickets.show', $ticket)->with('status', "Ticket {$ticket->ticket_number} raised successfully.");
    }

    public function show(Ticket $ticket)
    {
        $ticket->load([
            'location.client',
            'asset',
            'technician',
            'reportedBy',
            'evidence.uploadedBy',
            'visits.technician',
            'safetyChecklists.technician',
            'quotations.items',
            'invoices.payments',
            'parts.sparePart',
            'notifications',
            'massIncident',
        ]);
        $technicians = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Technician', 'Subcontractor / Vendor']))->get();
        $spareParts = SparePart::orderBy('name')->get();

        return view('tickets.show', compact('ticket', 'technicians', 'spareParts'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', 'in:Open,Assigned,In Progress,Parts Required,Scheduled,Completed,Overdue,Cancelled'],
            'assigned_technician_id' => ['nullable', 'exists:users,id'],
            'eta_display' => ['nullable', 'string', 'max:50'],
            'billing_type' => ['nullable', 'in:AMC Covered,Under Warranty,Chargeable'],
            'root_cause' => ['nullable', 'string', 'max:150'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        // Enforce Pre-Work Safety Checklist before starting work (#12)
        if ($request->status === 'In Progress' && !$ticket->safetyChecklists()->whereNotNull('verified_at')->exists()) {
            return back()->with('error', 'Pre-Work Safety Checklist (PPE & Electrical Isolation) must be completed before moving ticket to In Progress.');
        }

        // Enforce Root Cause when closing ticket (#23)
        if ($request->status === 'Completed' && !$request->filled('root_cause') && !$ticket->root_cause) {
            return back()->with('error', 'Please select a Root Cause before marking the ticket as Completed.');
        }

        $oldStatus = $ticket->status;
        $data = [
            'status' => $request->status,
            'assigned_technician_id' => $request->assigned_technician_id ?? $ticket->assigned_technician_id,
            'eta_display' => $request->eta_display ?? $ticket->eta_display,
            'billing_type' => $request->billing_type ?? $ticket->billing_type,
            'root_cause' => $request->root_cause ?? $ticket->root_cause,
            'resolution_notes' => $request->resolution_notes ?? $ticket->resolution_notes,
        ];

        if (!$ticket->first_response_at && ($request->filled('assigned_technician_id') || in_array($request->status, ['Assigned', 'In Progress', 'Completed']))) {
            $data['first_response_at'] = now();
        }

        if ($request->status === 'Completed' && !$ticket->completed_at) {
            $data['completed_at'] = now();
        }

        $ticket->update($data);

        if ($request->status === 'Assigned' || ($request->filled('assigned_technician_id') && $oldStatus !== $request->status)) {
            $techName = $ticket->fresh()->technician?->name ?? 'Technician';
            NotificationService::sendTicketAlert(
                $ticket,
                'technician_assigned',
                "Technician {$techName} assigned to ticket {$ticket->ticket_number} ({$ticket->location?->name})."
            );
        } elseif ($request->status === 'Completed' && $oldStatus !== 'Completed') {
            NotificationService::sendTicketAlert(
                $ticket,
                'work_completed',
                "Ticket {$ticket->ticket_number} at {$ticket->location?->name} is Completed. Please review After photos & rate the service."
            );
        }

        AuditLog::log('ticket_updated', "Updated ticket {$ticket->ticket_number} status from {$oldStatus} to {$request->status}.", $ticket);

        return back()->with('status', 'Ticket status and assignment updated.');
    }

    public function uploadEvidence(Request $request, Ticket $ticket)
    {
        $request->validate([
            'evidence_type' => ['required', 'in:before,after,during,jobcard'],
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $path = $request->file('photo')->store('evidence', 'public');

        TicketEvidence::create([
            'ticket_id' => $ticket->id,
            'evidence_type' => $request->evidence_type,
            'file_path' => $path,
            'caption' => $request->caption ?? ucfirst($request->evidence_type) . ' photo evidence',
            'uploaded_by_user_id' => auth()->id(),
            'captured_at' => now(),
        ]);

        AuditLog::log('evidence_uploaded', "Uploaded {$request->evidence_type} evidence for ticket {$ticket->ticket_number}.", $ticket);

        return back()->with('status', ucfirst($request->evidence_type) . ' evidence photo uploaded successfully.');
    }

    public function checkIn(Request $request, Ticket $ticket)
    {
        TicketVisit::create([
            'ticket_id' => $ticket->id,
            'technician_id' => auth()->id(),
            'checked_in_at' => now(),
            'checkin_lat' => $request->input('lat'),
            'checkin_lng' => $request->input('lng'),
            'visit_notes' => $request->input('visit_notes'),
        ]);

        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        AuditLog::log('technician_checkin', "Technician checked in on site for {$ticket->ticket_number}.", $ticket);

        return back()->with('status', 'Site Check-In recorded with timestamp & GPS.');
    }

    public function checkOut(Request $request, Ticket $ticket)
    {
        $visit = $ticket->visits()->whereNull('checked_out_at')->first();
        if (!$visit) {
            return back()->with('error', 'No active Check-In found. Please Check-In first.');
        }

        $visit->update([
            'checked_out_at' => now(),
            'checkout_lat' => $request->input('lat'),
            'checkout_lng' => $request->input('lng'),
            'travel_distance_km' => $request->input('travel_distance_km'),
            'vehicle_cost' => $request->input('vehicle_cost'),
            'visit_notes' => $request->input('visit_notes', $visit->visit_notes),
        ]);

        AuditLog::log('technician_checkout', "Technician checked out from site for {$ticket->ticket_number}.", $ticket);

        return back()->with('status', 'Site Check-Out recorded.');
    }

    public function submitSafetyChecklist(Request $request, Ticket $ticket)
    {
        $request->validate([
            'ppe_verified' => 'accepted',
            'electrical_isolation_confirmed' => 'accepted',
            'work_at_height_required' => 'nullable|boolean',
            'work_at_height_permit' => 'nullable|boolean',
            'permit_number' => 'nullable|string|max:100',
            'safety_notes' => 'nullable|string|max:500',
        ]);

        if ($request->boolean('work_at_height_required') && !$request->boolean('work_at_height_permit')) {
            return back()->with('error', 'Work-at-Height Permit confirmation is mandatory when height work is required.');
        }

        SafetyChecklist::create([
            'ticket_id' => $ticket->id,
            'technician_id' => auth()->id(),
            'ppe_verified' => true,
            'electrical_isolation_confirmed' => true,
            'work_at_height_required' => $request->boolean('work_at_height_required'),
            'work_at_height_permit' => $request->boolean('work_at_height_permit'),
            'permit_number' => $request->input('permit_number'),
            'safety_notes' => $request->input('safety_notes'),
            'verified_at' => now(),
        ]);

        AuditLog::log('safety_verified', "Pre-work safety checklist verified for {$ticket->ticket_number}.", $ticket);

        return back()->with('status', 'Pre-Work Safety Checklist verified! You can now start work.');
    }

    public function reopen(Request $request, Ticket $ticket)
    {
        $request->validate([
            'reopen_reason' => 'required|string|min:5|max:500',
        ]);

        $ticket->update([
            'status' => 'Open',
            'completed_at' => null,
            'reopen_count' => ($ticket->reopen_count ?? 0) + 1,
            'reopen_reason' => $request->reopen_reason,
            'reopened_at' => now(),
        ]);

        NotificationService::sendTicketAlert(
            $ticket,
            'ticket_reopened',
            "ALERT: Ticket {$ticket->ticket_number} was REOPENED by client. Reason: {$request->reopen_reason}"
        );

        AuditLog::log('ticket_reopened', "Ticket {$ticket->ticket_number} reopened: {$request->reopen_reason}", $ticket);

        return back()->with('status', "Ticket {$ticket->ticket_number} has been reopened for rework.");
    }

    public function rate(Request $request, Ticket $ticket)
    {
        $request->validate([
            'client_rating' => 'required|integer|min:1|max:5',
            'client_feedback' => 'nullable|string|max:500',
        ]);

        $ticket->update([
            'client_rating' => $request->client_rating,
            'client_feedback' => $request->client_feedback,
            'rated_at' => now(),
        ]);

        AuditLog::log('ticket_rated', "Client rated ticket {$ticket->ticket_number}: {$request->client_rating}/5 stars.", $ticket);

        return back()->with('status', 'Thank you! Your service rating has been recorded.');
    }

    public function attachPart(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $part = SparePart::findOrFail($validated['spare_part_id']);
        $qty = (int) $validated['quantity'];
        $totalCost = $qty * (float) $part->unit_cost;

        TicketPart::create([
            'ticket_id' => $ticket->id,
            'spare_part_id' => $part->id,
            'quantity' => $qty,
            'unit_cost' => $part->unit_cost,
            'total_cost' => $totalCost,
            'recorded_by' => auth()->id(),
        ]);

        $part->update(['current_stock' => max(0, $part->current_stock - $qty)]);

        AuditLog::log('part_consumed', "Used {$qty} {$part->unit} of {$part->name} on {$ticket->ticket_number}.", $ticket);

        return back()->with('status', "Recorded {$qty} {$part->unit} of {$part->name} (₹" . number_format($totalCost, 2) . ").");
    }
}
