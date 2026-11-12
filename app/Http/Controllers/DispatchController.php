<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', now()->toDateString());

        $technicians = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Technician', 'Subcontractor / Vendor']))
            ->orderBy('name')
            ->get();

        $assignedTickets = Ticket::with(['location', 'technician', 'visits'])
            ->where(function ($q) use ($selectedDate) {
                $q->whereDate('eta_date', $selectedDate)
                    ->orWhereDate('scheduled_at', $selectedDate)
                    ->orWhereIn('status', ['Open', 'Assigned', 'In Progress']);
            })
            ->orderBy('priority')
            ->get();

        $unassignedTickets = $assignedTickets->whereNull('assigned_technician_id');

        $pmVisits = MaintenanceSchedule::with(['location', 'technician'])
            ->whereDate('scheduled_date', $selectedDate)
            ->get();

        return view('dispatch.index', compact(
            'selectedDate', 'technicians', 'assignedTickets', 'unassignedTickets', 'pmVisits'
        ));
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_technician_id' => 'required|exists:users,id',
            'eta_date' => 'nullable|date',
        ]);

        $ticket->update([
            'assigned_technician_id' => $validated['assigned_technician_id'],
            'eta_date' => $validated['eta_date'] ?? now()->toDateString(),
            'eta_display' => $validated['eta_date'] ? \Carbon\Carbon::parse($validated['eta_date'])->format('d M') : 'Today',
            'status' => $ticket->status === 'Open' ? 'Assigned' : $ticket->status,
            'first_response_at' => $ticket->first_response_at ?? now(),
        ]);

        $tech = User::find($validated['assigned_technician_id']);
        NotificationService::sendTicketAlert(
            $ticket,
            'technician_assigned',
            "Technician {$tech->name} has been assigned to ticket {$ticket->ticket_number} at {$ticket->location?->name}."
        );

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Technician Dispatched',
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
            'description' => "Dispatched {$tech->name} to {$ticket->ticket_number}",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('status', "Assigned {$tech->name} to {$ticket->ticket_number}.");
    }
}
