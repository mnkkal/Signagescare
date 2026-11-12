<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Reports & Analytics Downloads</h1>
                <p class="text-xs text-slate-500 mt-0.5">Comprehensive audit reports, SLA compliance, location-wise service tickets, and CSV export</p>
            </div>
            <a href="{{ route('reports.export-tickets') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 shadow-sm transition-all flex-shrink-0">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export Tickets CSV</span>
            </a>
        </div>

        <!-- 2 Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tickets Breakdown -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                <h2 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Ticket Volume & Status Breakdown</h2>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Total Registered Tickets:</span>
                        <span class="font-bold text-slate-900">{{ $ticketStats['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sky-600">Open Tickets:</span>
                        <span class="font-bold text-sky-700">{{ $ticketStats['open'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-amber-600">In Progress:</span>
                        <span class="font-bold text-amber-700">{{ $ticketStats['in_progress'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-emerald-600">Resolved & Closed:</span>
                        <span class="font-bold text-emerald-700">{{ $ticketStats['completed'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-rose-600">Overdue (SLA Breached):</span>
                        <span class="font-bold text-rose-700">{{ $ticketStats['overdue'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Asset Health Summary -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                <h2 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Signage Network Asset Health</h2>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Total Monitored Facades:</span>
                        <span class="font-bold text-slate-900">{{ $assetStats['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-emerald-600">Healthy & Certified:</span>
                        <span class="font-bold text-emerald-700">{{ $assetStats['healthy'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-amber-600">Attention Required:</span>
                        <span class="font-bold text-amber-700">{{ $assetStats['attention'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-rose-600">Critical Condition:</span>
                        <span class="font-bold text-rose-700">{{ $assetStats['critical'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location-wise Service Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Store-wise Service & Maintenance Audit</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[580px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Store Location</th>
                            <th class="py-3 px-4">City</th>
                            <th class="py-3 px-4">Monitored Assets</th>
                            <th class="py-3 px-4">Total Tickets</th>
                            <th class="py-3 px-4">Resolved Tickets</th>
                            <th class="py-3 px-4">Resolution Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach ($locationReports as $loc)
                            @php
                                $rate = $loc->tickets_count > 0 ? round(($loc->completed_tickets_count / $loc->tickets_count) * 100) : 100;
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    {{ $loc->name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $loc->city }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-blue-600">
                                    {{ $loc->assets_count }} Assets
                                </td>
                                <td class="py-3.5 px-4 text-slate-800 font-bold">
                                    {{ $loc->tickets_count }}
                                </td>
                                <td class="py-3.5 px-4 text-emerald-600 font-bold">
                                    {{ $loc->completed_tickets_count }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $rate }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pilot KPI Summary Strip -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                <div class="text-xs font-semibold text-slate-500">First-Time-Fix Rate (FTFR)</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $ftfr }}%</div>
                <p class="text-[11px] text-slate-400 mt-1">Completed without reopen</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                <div class="text-xs font-semibold text-slate-500">Mean Time to Repair (MTTR)</div>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $mttrHours }} hrs</div>
                <p class="text-[11px] text-slate-400 mt-1">Average ticket resolution time</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                <div class="text-xs font-semibold text-slate-500">AMC Renewal Rate</div>
                <div class="text-2xl font-black text-indigo-600 mt-1">{{ $amcRenewalRate }}%</div>
                <p class="text-[11px] text-slate-400 mt-1">Active / renewed AMC contracts</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                <div class="text-xs font-semibold text-slate-500">Repeat-Failure Assets</div>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $repeatFailureAssets->count() }}</div>
                <p class="text-[11px] text-slate-400 mt-1">&ge;3 tickets in last 6 months</p>
            </div>
        </div>

        <!-- Revenue by Client & Branch -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Revenue by Client</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4 text-right">Billed Total (INR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($revenueByClient as $row)
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-slate-800">{{ $row->name }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-emerald-700">₹{{ number_format((float) ($row->total_billed ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-5 px-4 text-center text-slate-400">No invoices recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Revenue by Branch</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Branch</th>
                                <th class="py-3 px-4 text-right">Billed Total (INR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($revenueByBranch as $row)
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-slate-800">{{ $row->name }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-blue-700">₹{{ number_format((float) ($row->total_billed ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-5 px-4 text-center text-slate-400">No branch-level invoices recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Technician Productivity & Repeat-Failure Assets -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Technician Productivity</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Technician</th>
                                <th class="py-3 px-4">Open</th>
                                <th class="py-3 px-4">Completed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($technicianProductivity as $tech)
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-slate-800">{{ $tech->name }}</td>
                                    <td class="py-3 px-4">{{ $tech->open_tickets_count }}</td>
                                    <td class="py-3 px-4 font-bold text-emerald-600">{{ $tech->closed_tickets_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-5 px-4 text-center text-slate-400">No technician data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Repeat-Failure Assets (&ge;3 Tickets / 6 Mo)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Asset</th>
                                <th class="py-3 px-4">Location</th>
                                <th class="py-3 px-4">Tickets (6m)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($repeatFailureAssets as $rfa)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-rose-700">
                                        <a href="{{ route('assets.show', $rfa) }}" class="hover:underline">{{ $rfa->asset_tag }} &mdash; {{ $rfa->asset_name }}</a>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">{{ $rfa->location->name ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold">{{ $rfa->recent_failures }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-5 px-4 text-center text-slate-400">No repeat-failure assets flagged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
