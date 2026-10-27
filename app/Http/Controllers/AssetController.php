<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ClientLocation;
use App\Models\SignageAsset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = SignageAsset::with('location.client');

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
        return view('assets.create', compact('locations'));
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
            'health_status' => ['required', 'in:healthy,attention,critical'],
            'notes' => ['nullable', 'string'],
        ]);

        $asset = SignageAsset::create($request->all());

        AuditLog::log('asset_created', "Registered new signage asset {$asset->asset_tag} ({$asset->asset_name}).", $asset);

        return redirect()->route('assets.index')->with('status', "Asset {$asset->asset_tag} created successfully.");
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
}
