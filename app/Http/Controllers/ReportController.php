<?php

namespace App\Http\Controllers;

use App\Models\AmcContract;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\Invoice;
use App\Models\SignageAsset;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

        // Service Quality KPIs (#25)
        $completedTickets = Ticket::where('status', 'Completed')->whereNotNull('completed_at')->get();
        $ftfr = $completedTickets->count() > 0
            ? round(($completedTickets->where('reopen_count', 0)->count() / $completedTickets->count()) * 100, 1)
            : 100;
        $mttrHours = $completedTickets->count() > 0
            ? round($completedTickets->avg(fn ($t) => $t->created_at->diffInMinutes($t->completed_at) / 60), 1)
            : 0;

        // Revenue by Client & Branch (#32)
        $revenueByClient = Client::withSum('invoices as total_billed', 'total_amount')
            ->withSum('invoices as total_collected', 'paid_amount')
            ->get();

        $revenueByBranch = ClientLocation::withSum('invoices as total_billed', 'total_amount')
            ->withSum('invoices as total_collected', 'paid_amount')
            ->withCount(['tickets', 'assets'])
            ->withCount(['tickets as completed_tickets_count' => fn ($q) => $q->where('status', 'Completed')])
            ->get();

        // Technician Productivity (#34)
        $technicianProductivity = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Technician', 'Subcontractor / Vendor']))
            ->withCount(['assignedTickets as closed_tickets_count' => fn ($q) => $q->where('status', 'Completed')])
            ->withCount(['assignedTickets as open_tickets_count' => fn ($q) => $q->whereNotIn('status', ['Completed', 'Cancelled'])])
            ->get();

        // AMC Renewal Rate (#33)
        $totalAmc = AmcContract::count();
        $activeAmc = AmcContract::where('status', 'Active')->count();
        $amcRenewalRate = $totalAmc > 0 ? round(($activeAmc / $totalAmc) * 100, 1) : 100;

        // Repeat Failure Assets (#24)
        $repeatFailureAssets = SignageAsset::with('location')
            ->withCount(['tickets as recent_failures' => fn ($q) => $q->where('created_at', '>=', now()->subMonths(6))])
            ->get()
            ->filter(fn ($asset) => $asset->recent_failures >= 3)
            ->values();

        $locationReports = $revenueByBranch;

        return view('reports.index', compact(
            'ticketStats', 'assetStats', 'locationReports',
            'ftfr', 'mttrHours', 'revenueByClient', 'revenueByBranch',
            'technicianProductivity', 'amcRenewalRate', 'repeatFailureAssets'
        ));
    }

    public function exportTicketsCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="signage_care_tickets_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Ticket ID', 'Location', 'Asset', 'Problem', 'Priority', 'Billing Type', 'Root Cause', 'Status', 'Technician', 'Rating', 'Created At', 'Completed At']);

            Ticket::with(['location', 'asset', 'technician'])->chunk(100, function ($tickets) use ($handle) {
                foreach ($tickets as $ticket) {
                    fputcsv($handle, [
                        $ticket->ticket_number,
                        $ticket->location?->name ?? 'N/A',
                        $ticket->asset?->asset_tag ?? 'General Facade',
                        $ticket->problem_title,
                        $ticket->priority,
                        $ticket->billing_type ?? 'AMC Covered',
                        $ticket->root_cause ?? '-',
                        $ticket->status,
                        $ticket->technician?->name ?? 'Unassigned',
                        $ticket->client_rating ? "{$ticket->client_rating}/5" : '-',
                        $ticket->created_at->format('Y-m-d H:i'),
                        $ticket->completed_at ? $ticket->completed_at->format('Y-m-d H:i') : '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
