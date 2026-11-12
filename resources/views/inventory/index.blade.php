<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto" x-data="{ showPartModal: false, showSupplierModal: false }">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Spare Parts &amp; Suppliers Inventory</h1>
                <p class="text-xs text-slate-500 mt-0.5">Stock tracking for LEDs, SMPS, cables, acrylic, ACP, flex, low-stock alerts, and supplier directory</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($lowStockCount > 0)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        {{ $lowStockCount }} Low Stock Alert(s)
                    </span>
                @endif
                <button @click="showPartModal = true" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700">+ Add Spare Part</button>
                <button @click="showSupplierModal = true" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800">+ Add Supplier</button>
            </div>
        </div>

        <!-- Spare Parts Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Stock Register</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[680px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">SKU</th>
                            <th class="py-3 px-4">Item Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Unit Cost</th>
                            <th class="py-3 px-4">In Stock</th>
                            <th class="py-3 px-4 text-right">Quick Restock / Adjust</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($parts as $p)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 font-bold text-slate-700">{{ $p->sku }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $p->name }}</td>
                                <td class="py-3 px-4">{{ $p->category }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $p->supplier?->name ?? '-' }}</td>
                                <td class="py-3 px-4 font-semibold">&#8377;{{ number_format($p->unit_cost, 2) }} / {{ $p->unit }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $p->isLowStock() ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                        {{ $p->current_stock }} {{ $p->unit }} {{ $p->isLowStock() ? '(LOW)' : '' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('inventory.parts.adjust', $p) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf
                                        <input type="number" name="quantity_delta" value="10" class="w-16 text-[11px] py-1 rounded-lg border-slate-200" />
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-semibold">Adjust</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-slate-400">No spare parts added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Suppliers List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Approved Suppliers</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[580px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Supplier Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Contact</th>
                            <th class="py-3 px-4">GSTIN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($suppliers as $sup)
                            <tr>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $sup->name }}</td>
                                <td class="py-3 px-4">{{ $sup->category }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $sup->contact_person }} &middot; {{ $sup->phone }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $sup->gstin ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-400">No suppliers added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Part Modal -->
        <div x-show="showPartModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showPartModal = false">
                <h3 class="text-base font-bold text-slate-900 mb-4">Add Spare Part</h3>
                <form action="{{ route('inventory.parts.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">SKU</label>
                            <input type="text" name="sku" required placeholder="SMPS-24V-400W" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Category</label>
                            <select name="category" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                @foreach (['LEDs', 'SMPS', 'Cables', 'Acrylic', 'ACP', 'Flex', 'Hardware'] as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Part Name</label>
                        <input type="text" name="name" required placeholder="24V 400W Waterproof Rainproof SMPS" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700">Unit</label>
                            <input type="text" name="unit" value="pcs" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Stock</label>
                            <input type="number" name="current_stock" value="20" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Min Alert</label>
                            <input type="number" name="min_stock_alert" value="5" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Unit Cost (&#8377;)</label>
                            <input type="number" step="0.01" name="unit_cost" required placeholder="850" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Supplier</label>
                            <select name="supplier_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="">-- Optional --</option>
                                @foreach ($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showPartModal = false" class="px-3 py-2 rounded-xl text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold">Save Part</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Supplier Modal -->
        <div x-show="showSupplierModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showSupplierModal = false">
                <h3 class="text-base font-bold text-slate-900 mb-4">Add Supplier</h3>
                <form action="{{ route('inventory.suppliers.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="font-semibold text-slate-700">Supplier Name</label>
                        <input type="text" name="name" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Contact Person</label>
                            <input type="text" name="contact_person" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Phone</label>
                            <input type="text" name="phone" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Category</label>
                            <input type="text" name="category" value="LEDs & SMPS" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">GSTIN</label>
                            <input type="text" name="gstin" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showSupplierModal = false" class="px-3 py-2 rounded-xl text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
