<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\Supplier;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $parts = SparePart::with('supplier')->orderBy('category')->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $lowStockCount = $parts->filter(fn ($p) => $p->isLowStock())->count();

        return view('inventory.index', compact('parts', 'suppliers', 'lowStockCount'));
    }

    public function storePart(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:spare_parts,sku',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:30',
            'current_stock' => 'required|integer|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        SparePart::create($validated);

        return back()->with('status', "Spare part {$validated['name']} added to stock register.");
    }

    public function adjustStock(Request $request, SparePart $part)
    {
        $validated = $request->validate([
            'quantity_delta' => 'required|integer',
        ]);

        $newStock = max(0, $part->current_stock + $validated['quantity_delta']);
        $part->update(['current_stock' => $newStock]);

        return back()->with('status', "Stock updated for {$part->name}: {$newStock} {$part->unit}.");
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'category' => 'required|string|max:100',
        ]);

        Supplier::create($validated);

        return back()->with('status', "Supplier {$validated['name']} added.");
    }
}
