<?php

namespace App\Http\Controllers;

use App\Models\AssetDocument;
use App\Models\AuditLog;
use App\Models\ClientLocation;
use App\Models\SignageAsset;
use App\Models\SopProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SignageAsset::with(['location.client', 'sopProject', 'documents'])
            ->withCount(['tickets as recent_tickets_count' => fn ($q) => $q->where('created_at', '>=', now()->subMonths(6))]);

        if ($user && $user->hasRole('Store Manager') && $user->client_id) {
            $query->whereHas('location', fn ($q) => $q->where('client_id', $user->client_id));
        }

        if ($request->filled('health_status')) {
            $query->where('health_status', $request->health_status);
        }

        if ($request->filled('location_id')) {
            $query->where('client_location_id', $request->location_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('asset_name', 'like', "%{$search}%")
                  ->orWhere('sign_type', 'like', "%{$search}%");
            });
        }

        $assets = $query->paginate(15);
        $locations = ClientLocation::where('is_active', true)->get();

        $stats = [
            'total' => SignageAsset::count(),
            'healthy' => SignageAsset::where('health_status', 'healthy')->count(),
            'attention' => SignageAsset::where('health_status', 'attention')->count(),
            'critical' => SignageAsset::where('health_status', 'critical')->count(),
        ];

        return view('assets.index', compact('assets', 'locations', 'stats'));
    }

    public function create()
    {
        $locations = ClientLocation::where('is_active', true)->get();
        $sopProjects = SopProject::orderByDesc('id')->get();
        return view('assets.create', compact('locations', 'sopProjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_location_id' => ['required', 'exists:client_locations,id'],
            'asset_tag' => ['required', 'string', 'max:50', 'unique:signage_assets'],
            'asset_name' => ['required', 'string', 'max:255'],
            'sign_type' => ['required', 'string'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'illumination_type' => ['required', 'string'],
            'manufacturer_model' => ['nullable', 'string', 'max:150'],
            'power_rating_watts' => ['nullable', 'integer', 'min:0'],
            'gps_coordinates' => ['nullable', 'string', 'max:100'],
            'sop_project_id' => ['nullable', 'exists:sop_projects,id'],
            'installation_date' => ['nullable', 'date'],
            'warranty_expiry_date' => ['nullable', 'date'],
            'health_status' => ['required', 'in:healthy,attention,critical'],
            'notes' => ['nullable', 'string'],
        ]);

        $asset = SignageAsset::create($request->only([
            'client_location_id', 'asset_tag', 'asset_name', 'sign_type', 'dimensions',
            'illumination_type', 'manufacturer_model', 'power_rating_watts', 'gps_coordinates',
            'sop_project_id', 'installation_date', 'warranty_expiry_date', 'health_status', 'notes',
        ]));

        AuditLog::log('asset_created', "Registered new signage asset {$asset->asset_tag} ({$asset->asset_name}).", $asset);

        return redirect()->route('assets.index')->with('status', "Asset {$asset->asset_tag} created successfully.");
    }

    public function show(SignageAsset $asset)
    {
        $asset->load(['location.client', 'sopProject', 'tickets', 'documents.uploader']);
        return view('assets.show', compact('asset'));
    }

    public function updateHealth(Request $request, SignageAsset $asset)
    {
        $request->validate([
            'health_status' => ['required', 'in:healthy,attention,critical'],
            'notes' => ['nullable', 'string'],
        ]);

        $old = $asset->health_status;
        $asset->update([
            'health_status' => $request->health_status,
            'notes' => $request->notes ?? $asset->notes,
        ]);

        AuditLog::log('asset_health_updated', "Updated asset {$asset->asset_tag} health from {$old} to {$request->health_status}.", $asset);

        return back()->with('status', "Asset {$asset->asset_tag} condition updated to {$request->health_status}.");
    }

    public function uploadDocument(Request $request, SignageAsset $asset)
    {
        $request->validate([
            'document_type' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'expiry_date' => ['nullable', 'date'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $path = $request->file('file')->store('asset_documents', 'public');

        AssetDocument::create([
            'signage_asset_id' => $asset->id,
            'document_type' => $request->document_type,
            'title' => $request->title,
            'file_path' => $path,
            'expiry_date' => $request->expiry_date,
            'uploaded_by' => auth()->id(),
        ]);

        AuditLog::log('asset_document_uploaded', "Uploaded {$request->document_type} for asset {$asset->asset_tag}.", $asset);

        return back()->with('status', 'Compliance document uploaded.');
    }
}
