<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto" x-data="{ showModal: false }">

        <!-- Page Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Preventive Maintenance Schedules</h1>
                <p class="text-xs text-slate-500 mt-0.5">Recurring inspections, cleaning audits, electrical torque verification, and AMC schedule management</p>
            </div>
            <button @click="showModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Schedule Maintenance Visit</span>
            </button>
        </div>

        <!-- Schedule Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Store Location</th>
                            <th class="py-3 px-4">Scheduled Date</th>
                            <th class="py-3 px-4">Last Visit</th>
                            <th class="py-3 px-4">Technician</th>
                            <th class="py-3 px-4">Scope / Checklist</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach ($schedules as $s)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 font-bold text-slate-800 whitespace-nowrap">
                                    {{ $s->location->name ?? 'Nagpur' }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 whitespace-nowrap font-semibold">
                                    {{ $s->scheduled_date ? $s->scheduled_date->format('d M Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-400 whitespace-nowrap">
                                    {{ $s->last_visit_date ? $s->last_visit_date->format('d M Y') : 'First Visit' }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 whitespace-nowrap">
                                    {{ $s->technician->name ?? 'Unassigned' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 max-w-xs truncate">
                                    {{ $s->checklist_summary ?? 'Routine preventive cleaning & LED test' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        {{ $s->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <form action="{{ route('maintenance.update-status', $s) }}" method="POST" class="inline-flex items-center gap-1.5">
                                        @csrf
                                        <select name="status" class="text-[10px] py-1 px-1.5 rounded-lg border-slate-300">
                                            <option value="Scheduled" {{ $s->status === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="In Progress" {{ $s->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Completed" {{ $s->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Overdue" {{ $s->status === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                        </select>
                                        <button type="submit" class="px-2 py-1 text-[10px] font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">
                                            Save
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $schedules->links() }}
            </div>
        </div>

        <!-- Schedule Visit Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Schedule Preventive Maintenance</h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('maintenance.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="font-semibold text-slate-700">Location</label>
                        <select name="client_location_id" required class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            @foreach ($locations as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">AMC Contract</label>
                        <select name="amc_contract_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            @foreach ($contracts as $c)
                                <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->contract_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Assigned Technician</label>
                        <select name="assigned_technician_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            <option value="">-- Select Technician --</option>
                            @foreach ($technicians as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700">Scheduled Date</label>
                            <input type="date" name="scheduled_date" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Last Visit Date</label>
                            <input type="date" name="last_visit_date" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Checklist Instructions</label>
                        <textarea name="checklist_summary" rows="2" placeholder="e.g. Clean acrylic fascia, verify power draw, test emergency cutoff" class="w-full mt-1 text-xs rounded-xl border-slate-200"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Save Schedule</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
