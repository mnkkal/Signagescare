<?php

namespace App\Http\Controllers;

use App\Models\ClientLocation;
use App\Models\MaintenanceSchedule;
use App\Models\SignageAsset;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $ticketStats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'Open')->count(),
            'in_progress' => Ticket::where('status', 'In Progress')->count(),
            'completed' => Ticket::where('status', 'Completed')->count(),
            'overdue' => Ticket::where('status', 'Overdue')->count(),
        ];

        $assetStats = [
            'total' => SignageAsset::count(),
            'healthy' => SignageAsset::where('health_status', 'healthy')->count(),
            'attention' => SignageAsset::where('health_status', 'attention')->count(),
            'critical' => SignageAsset::where('health_status', 'critical')->count(),
        ];

        $locationReports = ClientLocation::withCount(['tickets', 'assets'])
            ->withCount(['tickets as completed_tickets_count' => fn($q) => $q->where('status', 'Completed')])
            ->get();

        return view('reports.index', compact('ticketStats', 'assetStats', 'locationReports'));
    }

    public function exportTicketsCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="signage_care_tickets_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Ticket ID', 'Location', 'Asset', 'Problem', 'Priority', 'Status', 'Technician', 'Created At', 'Completed At']);

            Ticket::with(['location', 'asset', 'technician'])->chunk(100, function ($tickets) use ($handle) {
                foreach ($tickets as $ticket) {
                    fputcsv($handle, [
                        $ticket->ticket_number,
                        $ticket->location?->name ?? 'N/A',
                        $ticket->asset?->asset_tag ?? 'General Facade',
                        $ticket->problem_title,
                        $ticket->priority,
                        $ticket->status,
                        $ticket->technician?->name ?? 'Unassigned',
                        $ticket->created_at->format('Y-m-d H:i'),
                        $ticket->completed_at ? $ticket->completed_at->format('Y-m-d H:i') : '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
