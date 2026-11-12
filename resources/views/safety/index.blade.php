<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto" x-data="{ showModal: false }">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Safety Incidents &amp; Near-Miss Log</h1>
                <p class="text-xs text-slate-500 mt-0.5">Track electrical hazards, height-work near-misses, and corrective safety actions</p>
            </div>
            <button @click="showModal = true" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-sm">
                + Report Safety Incident / Near-Miss
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[640px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Type &amp; Severity</th>
                            <th class="py-3 px-4">Location / Ticket</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($incidents as $inc)
                            <tr>
                                <td class="py-3 px-4 whitespace-nowrap font-semibold text-slate-700">{{ $inc->incident_date->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $inc->incident_type }}</div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200">{{ $inc->severity }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div>{{ $inc->location?->name ?? 'General' }}</div>
                                    @if ($inc->ticket)
                                        <a href="{{ route('tickets.show', $inc->ticket) }}" class="text-blue-600 font-semibold hover:underline">{{ $inc->ticket->ticket_number }}</a>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-600 max-w-xs">{{ $inc->description }}</td>
                                <td class="py-3 px-4 font-bold">{{ $inc->status }}</td>
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('safety.update-status', $inc) }}" method="POST" class="inline-flex items-center gap-1">
                                        @csrf
                                        <select name="status" class="text-[11px] py-1 rounded-lg border-slate-200">
                                            @foreach (['Open', 'Investigated', 'Closed'] as $st)
                                                <option value="{{ $st }}" {{ $inc->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-semibold">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-slate-400">Zero safety incidents recorded. Keep up safe field practices!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Report Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showModal = false">
                <h3 class="text-base font-bold text-slate-900 mb-4">Report Safety Incident / Near-Miss</h3>
                <form action="{{ route('safety.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Type</label>
                            <select name="incident_type" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="Near-Miss">Near-Miss</option>
                                <option value="Electrical Hazard">Electrical Hazard</option>
                                <option value="Fall Hazard">Fall Hazard</option>
                                <option value="Minor Injury">Minor Injury</option>
                                <option value="Property Damage">Property Damage</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Severity</label>
                            <select name="severity" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Location</label>
                            <select name="client_location_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="">-- Optional --</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Date</label>
                            <input type="date" name="incident_date" value="{{ date('Y-m-d') }}" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Description</label>
                        <textarea name="description" rows="3" required class="w-full mt-1 text-xs rounded-xl border-slate-200"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-3 py-2 rounded-xl text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-white font-semibold">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
