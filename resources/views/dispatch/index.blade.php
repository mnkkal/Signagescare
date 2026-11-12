<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Technician Dispatch Board</h1>
                <p class="text-xs text-slate-500 mt-0.5">Daily assignment calendar showing technician skills, active site visits, and unassigned tickets</p>
            </div>
            <form action="{{ route('dispatch.index') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-300" />
            </form>
        </div>

        <!-- Unassigned Tickets Queue -->
        @if ($unassignedTickets->count() > 0)
            <div class="bg-amber-50/70 rounded-2xl p-4 sm:p-5 border border-amber-200">
                <h2 class="text-sm font-bold text-amber-900 mb-3">Unassigned Tickets Queue ({{ $unassignedTickets->count() }})</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($unassignedTickets as $t)
                        <div class="bg-white rounded-xl p-3.5 border border-amber-200/80 shadow-sm text-xs space-y-2">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('tickets.show', $t) }}" class="font-bold text-blue-600 hover:underline">{{ $t->ticket_number }}</a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $t->priority_badge_color }}">{{ $t->priority }}</span>
                            </div>
                            <div class="font-semibold text-slate-800">{{ $t->location?->name }} &middot; {{ $t->problem_title }}</div>
                            <form action="{{ route('dispatch.assign', $t) }}" method="POST" class="flex items-center gap-2 pt-1">
                                @csrf
                                <input type="hidden" name="eta_date" value="{{ $selectedDate }}">
                                <select name="assigned_technician_id" required class="flex-1 text-[11px] rounded-lg border-slate-200">
                                    <option value="">-- Assign Tech --</option>
                                    @foreach ($technicians as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-semibold text-[11px] hover:bg-blue-700">Dispatch</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Technician Columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($technicians as $tech)
                @php
                    $techTickets = $assignedTickets->where('assigned_technician_id', $tech->id);
                    $techPms = $pmVisits->where('assigned_technician_id', $tech->id);
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col">
                    <div class="px-5 py-4 bg-slate-900 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-bold text-sm">{{ $tech->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $tech->getRoleNames()->first() }} &middot; {{ $tech->phone ?? 'No phone' }}</div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-600 text-white">
                                {{ $techTickets->count() + $techPms->count() }} Jobs
                            </span>
                        </div>
                        @if (!empty($tech->skills))
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach ($tech->skills as $skill)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="p-4 space-y-3 text-xs flex-1">
                        @forelse ($techTickets as $tt)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('tickets.show', $tt) }}" class="font-bold text-blue-600 hover:underline">{{ $tt->ticket_number }}</a>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $tt->status_badge_color }}">{{ $tt->status }}</span>
                                </div>
                                <div class="font-semibold text-slate-800">{{ $tt->location?->name }}</div>
                                <div class="text-slate-500">{{ $tt->problem_title }}</div>
                                @php $latestVisit = $tt->visits->first(); @endphp
                                @if ($latestVisit)
                                    <div class="text-[10px] text-emerald-700 font-semibold pt-1">
                                        Checked In: {{ $latestVisit->checked_in_at?->format('h:i A') }}
                                        @if ($latestVisit->checked_out_at)
                                            &middot; Out: {{ $latestVisit->checked_out_at->format('h:i A') }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            @if ($techPms->isEmpty())
                                <div class="text-slate-400 text-center py-6">No jobs scheduled for {{ $selectedDate }}.</div>
                            @endif
                        @endforelse

                        @foreach ($techPms as $pm)
                            <div class="p-3 rounded-xl bg-sky-50 border border-sky-200 space-y-1">
                                <div class="text-[10px] font-bold uppercase text-sky-700">Preventive Maintenance</div>
                                <div class="font-semibold text-slate-800">{{ $pm->location?->name }}</div>
                                <div class="text-slate-600">{{ $pm->checklist_summary ?? 'Routine PM Inspection' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>
