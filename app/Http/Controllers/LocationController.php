<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientLocation::with(['client', 'storeManager', 'assets', 'tickets']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('store_code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $locations = $query->paginate(12);
        $clients = Client::where('is_active', true)->get();
        $storeManagers = User::role('Store Manager')->get();

        return view('locations.index', compact('locations', 'clients', 'storeManagers'));
    }

    public function show(ClientLocation $location)
    {
        $location->load(['client', 'storeManager', 'assets', 'tickets.technician', 'maintenanceSchedules']);
        return view('locations.show', compact('location'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'store_code' => ['required', 'string', 'max:50', 'unique:client_locations'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'store_manager_user_id' => ['nullable', 'exists:users,id'],
            'primary_contact_number' => ['nullable', 'string', 'max:20'],
        ]);

        $location = ClientLocation::create(array_merge($request->all(), ['is_active' => true]));

        AuditLog::log('location_created', "Added client location {$location->name}.", $location);

        return redirect()->route('locations.index')->with('status', "Location {$location->name} created successfully.");
    }
}
