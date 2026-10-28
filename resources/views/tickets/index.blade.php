<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Service Tickets</h1>
                <p class="text-xs text-slate-500 mt-0.5">Track, assign, and resolve breakdown support tickets across all signage locations</p>
            </div>
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Raise New Service Request</span>
            </a>
        </div>

        <!-- Filters Bar -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80">
            <form action="{{ route('tickets.index') }}" method="GET" class="flex flex-wrap items-center gap-2 sm:gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..." class="text-xs rounded-xl border-slate-200 w-full sm:w-48" />
                
                <select name="status" class="text-xs rounded-xl border-slate-200 flex-1 sm:flex-none" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['Open', 'Assigned', 'In Progress', 'Parts Required', 'Scheduled', 'Completed', 'Overdue'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>

                <select name="priority" class="text-xs rounded-xl border-slate-200 flex-1 sm:flex-none" onchange="this.form.submit()">
                    <option value="">All Priorities</option>
                    @foreach (['P1', 'P2', 'P3', 'P4'] as $pr)
                        <option value="{{ $pr }}" {{ request('priority') === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                    @endforeach
                </select>

                <select name="location_id" class="text-xs rounded-xl border-slate-200 w-full sm:w-auto" onchange="this.form.submit()">
                    <option value="">All Locations</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold">Filter</button>
                    @if (request()->hasAny(['search', 'status', 'priority', 'location_id']))
                        <a href="{{ route('tickets.index') }}" class="text-xs text-rose-600 hover:underline px-2">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[640px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-4">Ticket ID</th>
                            <th class="py-3.5 px-4">Location</th>
                            <th class="py-3.5 px-4">Problem Summary</th>
                            <th class="py-3.5 px-4">Priority</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Technician</th>
                            <th class="py-3.5 px-4">ETA</th>
                            <th class="py-3.5 px-4 text-right">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse ($tickets as $t)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3.5 px-4 font-bold text-blue-600 whitespace-nowrap">
                                    <a href="{{ route('tickets.show', $t) }}" class="hover:underline">
                                        {{ $t->ticket_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-slate-800 whitespace-nowrap">
                                    {{ $t->location->name ?? 'General' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-900 font-semibold max-w-xs truncate">
                                    {{ $t->problem_title }}
                                    <span class="block text-[11px] text-slate-400 font-normal">{{ $t->category }}</span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $t->priority_badge_color }}">
                                        {{ $t->priority }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold border {{ $t->status_badge_color }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $t->technician->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap font-medium">
                                    {{ $t->eta_display ?? 'Today' }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('tickets.show', $t) }}" class="px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-blue-600 hover:text-white rounded-lg transition-colors">
                                        Details &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No tickets found matching the criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $tickets->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
