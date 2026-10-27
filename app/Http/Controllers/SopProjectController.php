<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\SopChecklist;
use App\Models\SopProject;
use App\Models\User;
use Illuminate\Http\Request;

class SopProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = SopProject::with(['client', 'location', 'manager', 'supervisor', 'checklists']);

        if ($request->filled('stage')) {
            $query->where('current_stage', $request->stage);
        }

        $projects = $query->latest()->paginate(10);
        $clients = Client::where('is_active', true)->get();
        $locations = ClientLocation::where('is_active', true)->get();
        $supervisors = User::role(['Super Admin', 'Operations Manager', 'Production Supervisor'])->get();

        return view('sop.index', compact('projects', 'clients', 'locations', 'supervisors'));
    }

    public function show(SopProject $project)
    {
        $project->load(['client', 'location', 'ticket', 'manager', 'supervisor', 'checklists.verifier']);
        $stages = [
            'sales_requirement' => '1. Sales / Client Requirement',
            'site_survey_recce' => '2. Site Survey & Measurement (Recce)',
            'design_creative' => '3. Design / Creative & Jobcard',
            'fabrication' => '4. Fabrication',
            'production' => '5. Production & Quality',
            'packing_dispatch' => '6. Packing & Dispatch',
            'installation' => '7. Installation & Erection',
            'final_handover' => '8. Final Handover & Warranty',
        ];

        return view('sop.show', compact('project', 'stages'));
    }

    public function updateChecklist(Request $request, SopChecklist $checklist)
    {
        $request->validate([
            'status' => ['required', 'in:yes,no,na'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $checklist->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'verified_by_user_id' => auth()->id(),
            'verified_at' => now(),
        ]);

        AuditLog::log('sop_checklist_updated', "Updated SOP item '{$checklist->checklist_item}' to {$request->status}.", $checklist);

        return back()->with('status', 'Checklist item updated.');
    }

    public function advanceStage(Request $request, SopProject $project)
    {
        $request->validate([
            'current_stage' => ['required', 'string'],
            'overall_status' => ['required', 'string'],
        ]);

        $project->update([
            'current_stage' => $request->current_stage,
            'overall_status' => $request->overall_status,
        ]);

        AuditLog::log('sop_stage_advanced', "Advanced project {$project->project_code} stage to {$request->current_stage}.", $project);

        return back()->with('status', 'Project stage advanced successfully.');
    }
}
