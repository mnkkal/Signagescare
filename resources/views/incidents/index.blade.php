<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <div class="pb-4 border-b border-slate-200">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Storm &amp; Mass Incident Bulk Ticketing</h1>
            <p class="text-xs text-slate-500 mt-0.5">Raise a single parent incident for storms, monsoon outages, or mall shutdowns and auto-create tickets across selected branches</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200/80">
                <h2 class="text-sm font-bold text-slate-900 mb-3">Create Mass Incident &amp; Bulk Tickets</h2>
                <form action="{{ route('incidents.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="font-semibold text-slate-700">Incident Headline</label>
                        <input type="text" name="title" required placeholder="e.g. Nagpur Heavy Wind & Rain Storm" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Type</label>
                            <select name="incident_type" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="Storm">Storm / High Wind</option>
                                <option value="Monsoon">Monsoon Water Ingress</option>
                                <option value="Grid Outage">Power Surge / Outage</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Priority</label>
                            <select name="priority" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="P1">P1 - Critical</option>
                                <option value="P2" selected>P2 - High</option>
                                <option value="P3">P3 - Medium</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Problem Category</label>
                        <select name="category" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            <option value="Physical Damage">Storm / Physical Damage</option>
                            <option value="Main Sign OFF">Main Sign OFF</option>
                            <option value="Sign Loose">Sign Loose / Structural Check</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Select Affected Branches</label>
                        <div class="max-h-40 overflow-y-auto border border-slate-200 rounded-xl p-2.5 space-y-1.5 bg-slate-50">
                            @foreach ($locations as $loc)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}" class="rounded border-slate-300 text-blue-600" />
                                    <span class="font-medium text-slate-700">{{ $loc->name }} ({{ $loc->city }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Notes</label>
                        <textarea name="description" rows="2" class="w-full mt-1 text-xs rounded-xl border-slate-200"></textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                        Create Parent Incident &amp; Bulk Tickets
                    </button>
                </form>
            </div>

            <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-200/80">
                <h2 class="text-sm font-bold text-slate-900 mb-3">Active &amp; Past Mass Incidents</h2>
                <div class="space-y-3 text-xs">
                    @forelse ($incidents as $inc)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-blue-600">{{ $inc->incident_code }}: {{ $inc->title }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $inc->tickets->count() }} Linked Tickets
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">{{ $inc->incident_type }} &middot; {{ $inc->occurred_at?->format('d M Y, h:i A') }}</div>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                @foreach ($inc->tickets as $t)
                                    <a href="{{ route('tickets.show', $t) }}" class="px-2 py-0.5 rounded bg-white border border-slate-200 text-[10px] font-semibold text-blue-600 hover:underline">
                                        {{ $t->ticket_number }} ({{ $t->location?->name }})
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-slate-400 text-center py-8">No mass incidents recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
