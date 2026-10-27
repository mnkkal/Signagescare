<?php

namespace App\Http\Controllers;

use App\Models\AmcContract;
use App\Models\AuditLog;
use App\Models\ClientLocation;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceSchedule::with(['location.client', 'technician', 'contract']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('scheduled_date', 'asc')->paginate(15);
        $locations = ClientLocation::where('is_active', true)->get();
        $technicians = User::role('Technician')->get();
        $contracts = AmcContract::where('status', 'active')->get();

        return view('maintenance.index', compact('schedules', 'locations', 'technicians', 'contracts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_location_id' => ['required', 'exists:client_locations,id'],
            'amc_contract_id' => ['nullable', 'exists:amc_contracts,id'],
            'assigned_technician_id' => ['nullable', 'exists:users,id'],
            'scheduled_date' => ['required', 'date'],
            'last_visit_date' => ['nullable', 'date'],
            'checklist_summary' => ['nullable', 'string'],
        ]);

        $schedule = MaintenanceSchedule::create(array_merge($request->all(), [
            'status' => 'Scheduled',
            'next_visit_date' => $request->scheduled_date,
        ]));

        AuditLog::log('maintenance_scheduled', "Scheduled preventive maintenance for location ID {$schedule->client_location_id}.", $schedule);

        return redirect()->route('maintenance.index')->with('status', 'Preventive maintenance visit scheduled successfully.');
    }

    public function updateStatus(Request $request, MaintenanceSchedule $schedule)
    {
        $request->validate([
            'status' => ['required', 'in:Scheduled,In Progress,Completed,Overdue,Cancelled'],
            'findings' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $schedule->update([
            'status' => $request->status,
            'findings' => $request->findings ?? $schedule->findings,
            'notes' => $request->notes ?? $schedule->notes,
            'completed_at' => ($request->status === 'Completed') ? now() : $schedule->completed_at,
        ]);

        AuditLog::log('maintenance_updated', "Updated maintenance status to {$request->status}.", $schedule);

        return back()->with('status', 'Maintenance schedule status updated.');
    }
}
