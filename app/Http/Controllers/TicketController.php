<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ClientLocation;
use App\Models\SignageAsset;
use App\Models\Ticket;
use App\Models\TicketEvidence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['location', 'asset', 'technician', 'reportedBy']);

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
                  ->orWhereHas('location', fn($l) => $l->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('asset', fn($a) => $a->where('asset_tag', 'like', "%{$search}%")->orWhere('asset_name', 'like', "%{$search}%"));
            });
        }

        $tickets = $query->latest()->paginate(15);
        $locations = ClientLocation::where('is_active', true)->get();
        $technicians = User::role('Technician')->get();

        return view('tickets.index', compact('tickets', 'locations', 'technicians'));
    }

    public function create()
    {
        $locations = ClientLocation::with('assets')->where('is_active', true)->get();
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
            'problem_description' => ['required', 'string'],
            'issue_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $location = ClientLocation::findOrFail($request->client_location_id);
        
        // Generate formatted collision-free ticket number e.g. SC-NAG-0089
        $cityCode = strtoupper(substr($location->city ?? 'NAG', 0, 3));
        $seedNum = (int) (Ticket::max('id') ?? 0) + 100;
        do {
            $ticketNumber = sprintf('SC-%s-%04d', $cityCode, $seedNum++);
        } while (Ticket::where('ticket_number', $ticketNumber)->exists());

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'client_location_id' => $request->client_location_id,
            'signage_asset_id' => $request->signage_asset_id,
            'reported_by_user_id' => auth()->id(),
            'priority' => $request->priority,
            'status' => 'Open',
            'category' => $request->category,
            'problem_title' => $request->problem_title,
            'problem_description' => $request->problem_description,
            'eta_display' => 'Pending Review',
        ]);

        // If image uploaded with request, store as 'before' evidence
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

        AuditLog::log('ticket_created', "Service ticket {$ticket->ticket_number} created for {$location->name}.", $ticket);

        return redirect()->route('tickets.show', $ticket)->with('status', "Ticket {$ticket->ticket_number} raised successfully.");
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['location.client', 'asset', 'technician', 'reportedBy', 'evidence.uploadedBy']);
        $technicians = User::role('Technician')->get();

        return view('tickets.show', compact('ticket', 'technicians'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', 'in:Open,Assigned,In Progress,Parts Required,Scheduled,Completed,Overdue,Cancelled'],
            'assigned_technician_id' => ['nullable', 'exists:users,id'],
            'eta_display' => ['nullable', 'string', 'max:50'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $oldStatus = $ticket->status;
        $data = [
            'status' => $request->status,
            'assigned_technician_id' => $request->assigned_technician_id ?? $ticket->assigned_technician_id,
            'eta_display' => $request->eta_display ?? $ticket->eta_display,
            'resolution_notes' => $request->resolution_notes ?? $ticket->resolution_notes,
        ];

        if ($request->status === 'Completed' && !$ticket->completed_at) {
            $data['completed_at'] = now();
        }

        $ticket->update($data);

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

        $evidence = TicketEvidence::create([
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
}
