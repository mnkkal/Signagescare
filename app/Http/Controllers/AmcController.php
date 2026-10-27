<?php

namespace App\Http\Controllers;

use App\Models\AmcContract;
use App\Models\Client;
use App\Models\ClientLocation;
use Illuminate\Http\Request;

class AmcController extends Controller
{
    public function index(Request $request)
    {
        $contracts = AmcContract::with(['client', 'locations.assets'])->paginate(10);
        $clients = Client::where('is_active', true)->get();
        $locations = ClientLocation::where('is_active', true)->get();

        return view('amc.index', compact('contracts', 'clients', 'locations'));
    }

    public function show(AmcContract $amc)
    {
        $amc->load(['client', 'locations.assets', 'maintenanceSchedules.technician']);
        return view('amc.show', compact('amc'));
    }
}
