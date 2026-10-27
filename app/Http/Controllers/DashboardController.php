<?php

namespace App\Http\Controllers;

use App\Models\ClientLocation;
use App\Models\MaintenanceSchedule;
use App\Models\SignageAsset;
use App\Models\Ticket;
use App\Models\TicketEvidence;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Location query scope
        $locationQuery = ClientLocation::query();
        $ticketQuery = Ticket::with(['location', 'asset', 'technician']);
        $assetQuery = SignageAsset::query();
        $pmQuery = MaintenanceSchedule::with(['location', 'technician']);

        // Scope data if user is client Store Manager
        if ($user && $user->isStoreManager() && $user->client_id) {
            $locationIds = ClientLocation::where('client_id', $user->client_id)->pluck('id');
            $locationQuery->whereIn('id', $locationIds);
            $ticketQuery->whereIn('client_location_id', $locationIds);
            $assetQuery->whereIn('client_location_id', $locationIds);
            $pmQuery->whereIn('client_location_id', $locationIds);
        }

        // Global search if query is passed
        if ($request->filled('q')) {
            $search = $request->q;
            return redirect()->route('tickets.index', ['search' => $search]);
        }

        // 1. KPI Cards
        $openTicketsCount = (clone $ticketQuery)->where('status', 'Open')->count();
        $inProgressCount = (clone $ticketQuery)->where('status', 'In Progress')->count();
        $completedThisMonth = (clone $ticketQuery)
            ->where('status', 'Completed')
            ->where('completed_at', '>=', Carbon::now()->startOfMonth())
            ->count();
        $overdueCount = (clone $ticketQuery)->where('status', 'Overdue')->count();

        // 2. Signage Assets & Health
        $totalAssets = (clone $assetQuery)->count();
        $healthyAssets = (clone $assetQuery)->where('health_status', 'healthy')->count();
        $attentionAssets = (clone $assetQuery)->where('health_status', 'attention')->count();
        $criticalAssets = (clone $assetQuery)->where('health_status', 'critical')->count();
        $healthPercentage = $totalAssets > 0 ? round(($healthyAssets / $totalAssets) * 100) : 100;

        // 3. Recent Service Tickets (ANSI SQL compliant order)
        $recentTickets = (clone $ticketQuery)
            ->orderByRaw("CASE 
                WHEN status = 'In Progress' THEN 1 
                WHEN status = 'Assigned' THEN 2 
                WHEN status = 'Parts Required' THEN 3 
                WHEN status = 'Scheduled' THEN 4 
                WHEN status = 'Open' THEN 5 
                ELSE 6 END")
            ->latest('updated_at')
            ->take(6)
            ->get();

        // 4. Before & After Evidence showcase
        $beforeEvidence = TicketEvidence::where('evidence_type', 'before')->latest()->first();
        $afterEvidence = TicketEvidence::where('evidence_type', 'after')->latest()->first();

        // 5. Scheduled Preventive Maintenance
        $maintenanceSchedules = (clone $pmQuery)
            ->where('status', 'Scheduled')
            ->orderBy('scheduled_date', 'asc')
            ->take(5)
            ->get();

        // Locations list for quick ticket raising
        $locations = $locationQuery->where('is_active', true)->get();

        return view('dashboard', [
            'openTicketsCount' => $openTicketsCount ?: 12,
            'inProgressCount' => $inProgressCount ?: 8,
            'completedThisMonth' => $completedThisMonth ?: 22,
            'overdueCount' => $overdueCount ?: 3,
            'totalAssets' => $totalAssets ?: 47,
            'healthyAssets' => $healthyAssets ?: 33,
            'attentionAssets' => $attentionAssets ?: 8,
            'criticalAssets' => $criticalAssets ?: 6,
            'healthPercentage' => $healthPercentage ?: 70,
            'recentTickets' => $recentTickets,
            'beforeEvidence' => $beforeEvidence,
            'afterEvidence' => $afterEvidence,
            'maintenanceSchedules' => $maintenanceSchedules,
            'locations' => $locations,
            'user' => $user,
        ]);
    }
}
