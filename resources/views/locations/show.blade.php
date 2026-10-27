<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between pb-4 border-b border-slate-200 gap-4">
            <div>
                <a href="{{ route('locations.index') }}" class="text-xs font-semibold text-blue-600 hover:underline mb-1 inline-block">&larr; Back to My Locations</a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $location->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $location->store_code }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $location->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $location->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $location->is_active ? 'Active Location' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">{{ $location->address }}, {{ $location->city }}, {{ $location->state }} - {{ $location->pincode }}</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('tickets.create') }}?location_id={{ $location->id }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Raise Ticket for this Store</span>
                </a>
            </div>
        </div>

        <!-- Store Overview Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="text-[11px] text-slate-500 font-semibold uppercase">Total Signages</div>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $location->assets->count() }} Assets</div>
                <div class="text-[10px] text-emerald-600 mt-0.5">{{ $location->assets->where('health_status', 'healthy')->count() }} Healthy</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="text-[11px] text-slate-500 font-semibold uppercase">Open Tickets</div>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $location->tickets->where('status', 'Open')->count() }}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">Awaiting dispatch</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="text-[11px] text-slate-500 font-semibold uppercase">Resolved Tickets</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $location->tickets->where('status', 'Completed')->count() }}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">With photographic proof</div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="text-[11px] text-slate-500 font-semibold uppercase">Store Manager</div>
                <div class="text-sm font-bold text-slate-900 mt-1">{{ $location->storeManager->name ?? 'Store Manager' }}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">{{ $location->primary_contact_number ?? '+91 8208900802' }}</div>
            </div>
        </div>

        <!-- Signage Assets at this Location -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Signage Assets at this Location</h3>
                    <p class="text-xs text-slate-500">All registered boards, letter sets, and lightboxes</p>
                </div>
                <a href="{{ route('assets.create') }}?location_id={{ $location->id }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    + Register Signage
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Tag</th>
                            <th class="py-3 px-4">Signage Name</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Dimensions</th>
                            <th class="py-3 px-4">Illumination</th>
                            <th class="py-3 px-4">Condition</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse ($location->assets as $asset)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 font-bold text-blue-600 whitespace-nowrap">{{ $asset->asset_tag }}</td>
                                <td class="py-3 px-4 text-slate-900 font-semibold">{{ $asset->asset_name }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $asset->sign_type }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $asset->dimensions ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $asset->illumination_type }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $asset->health_status === 'healthy' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($asset->health_status === 'attention' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                        {{ ucfirst($asset->health_status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">No signage assets registered for this location yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Service History at this Location -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Service Tickets & Maintenance History</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Ticket ID</th>
                            <th class="py-3 px-4">Problem</th>
                            <th class="py-3 px-4">Priority</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Technician</th>
                            <th class="py-3 px-4">Created</th>
                            <th class="py-3 px-4 text-right">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse ($location->tickets as $t)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 font-bold text-blue-600">{{ $t->ticket_number }}</td>
                                <td class="py-3 px-4 text-slate-900 font-semibold">{{ $t->problem_title }}</td>
                                <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $t->priority_badge_color }}">{{ $t->priority }}</span></td>
                                <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $t->status_badge_color }}">{{ $t->status }}</span></td>
                                <td class="py-3 px-4 text-slate-600">{{ $t->technician->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-400">{{ $t->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('tickets.show', $t) }}" class="text-xs font-semibold text-blue-600 hover:underline">Details &rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400">No service tickets recorded for this location yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
