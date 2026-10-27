<?php

namespace App\Http\Controllers;

use App\Models\AmcContract;
use App\Models\ClientLocation;
use App\Models\SignageAsset;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function index()
    {
        $documents = [
            [
                'id' => 'tickets_csv',
                'title' => 'Complete Service Tickets Register (CSV)',
                'description' => 'Real-time tabular export of all service tickets, priority ratings, technician assignments and resolution timestamps.',
                'category' => 'Service Records',
                'format' => 'CSV',
                'download_route' => 'reports.export-tickets',
            ],
            [
                'id' => 'assets_csv',
                'title' => 'Signage Assets Master Register (CSV)',
                'description' => 'Full inventory of 47 monitored signage facades, dimensions, illumination types, and health classifications.',
                'category' => 'Asset Audits',
                'format' => 'CSV',
                'download_route' => 'downloads.export-assets',
            ],
            [
                'id' => 'sop_checklist_doc',
                'title' => 'Signage Standard Operating Procedures (SOP Checklist)',
                'description' => 'Standard operating procedure document covering Sales, Recce, Creative, Fabrication, Production, and Installation checklists.',
                'category' => 'Operations & SOP',
                'format' => 'DOCX',
                'download_route' => 'downloads.download-sop',
            ],
            [
                'id' => 'crm_proposal_pdf',
                'title' => 'Signage Care CRM & Service Platform Proposal',
                'description' => 'Comprehensive architecture and project scope document for multi-location retail signage care.',
                'category' => 'Contracts & Proposals',
                'format' => 'PDF',
                'download_route' => 'downloads.download-proposal',
            ],
        ];

        return view('downloads.index', compact('documents'));
    }

    public function exportAssetsCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="signage_assets_master_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Asset Tag', 'Signage Name', 'Location', 'City', 'Sign Type', 'Dimensions', 'Illumination', 'Condition']);

            SignageAsset::with('location')->chunk(100, function ($assets) use ($handle) {
                foreach ($assets as $asset) {
                    fputcsv($handle, [
                        $asset->asset_tag,
                        $asset->asset_name,
                        $asset->location?->name ?? 'N/A',
                        $asset->location?->city ?? 'Nagpur',
                        $asset->sign_type,
                        $asset->dimensions ?? '-',
                        $asset->illumination_type,
                        ucfirst($asset->health_status),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function downloadSop()
    {
        $file = base_path('SOP (final).docx');
        if (file_exists($file)) {
            return response()->download($file, 'Signage_Company_SOP_Standard.docx');
        }
        return back()->with('error', 'SOP file not found on disk.');
    }

    public function downloadProposal()
    {
        $file = base_path('Curve_Metrics_Signage_Care_CRM_Proposal.pdf');
        if (file_exists($file)) {
            return response()->download($file, 'Signage_Care_CRM_Platform_Proposal.pdf');
        }
        return back()->with('error', 'Proposal file not found on disk.');
    }
}
