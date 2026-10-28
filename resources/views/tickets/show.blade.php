<x-app-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-4">
            <div>
                <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:underline mb-1 inline-block">&larr; Back to Tickets</a>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $ticket->ticket_number }}</h1>
                    <span class="px-2.5 py-0.5 rounded text-xs font-bold border {{ $ticket->priority_badge_color }}">
                        {{ $ticket->priority }}
                    </span>
                    <span class="px-3 py-0.5 rounded-full text-xs font-semibold border {{ $ticket->status_badge_color }}">
                        {{ $ticket->status }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Location: <span class="font-bold text-slate-800">{{ $ticket->location->name ?? 'General' }}</span> &middot; Raised: {{ $ticket->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-xs text-slate-500 font-semibold">Assigned Technician</div>
                <div class="text-sm font-bold text-slate-900">{{ $ticket->technician->name ?? 'Unassigned' }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Col (2 cols): Details & Evidence -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Problem Overview Card -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                    <h2 class="text-base font-bold text-slate-900 mb-1">{{ $ticket->problem_title }}</h2>
                    <div class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 mb-4">
                        Category: {{ $ticket->category }}
                    </div>
                    <div class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 mb-4 whitespace-pre-line">
                        {{ $ticket->problem_description }}
                    </div>

                    @if ($ticket->resolution_notes)
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs">
                            <span class="font-bold text-emerald-900 block mb-1">Resolution Summary:</span>
                            <p class="text-emerald-800">{{ $ticket->resolution_notes }}</p>
                            @if ($ticket->completed_at)
                                <p class="text-[10px] text-emerald-600 mt-2 font-medium">Completed on: {{ $ticket->completed_at->format('d M Y, h:i A') }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Photographic Evidence Section (Before & After) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Before & After Photographic Evidence</h3>
                            <p class="text-xs text-slate-500">Visual proof of defect and completed rectification</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <!-- Before Box -->
                        <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50 p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-white">Before Service</span>
                                <span class="text-[10px] text-slate-400">Defect Evidence</span>
                            </div>
                            @php $beforePhoto = $ticket->beforePhotos()->first(); @endphp
                            @if ($beforePhoto)
                                <div class="h-40 rounded-lg overflow-hidden bg-slate-900 flex items-center justify-center text-center p-4">
                                    <div class="text-slate-300 text-xs">
                                        <div class="font-bold text-slate-200">oo lenskart</div>
                                        <div class="text-[10px] text-rose-400 mt-1">{{ $beforePhoto->caption ?? 'Defective Signage' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="h-40 rounded-lg border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-400 text-xs text-center p-4">
                                    No Before photograph uploaded yet.
                                </div>
                            @endif
                        </div>

                        <!-- After Box -->
                        <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50 p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white">After Service</span>
                                <span class="text-[10px] text-slate-400">Resolution Evidence</span>
                            </div>
                            @php $afterPhoto = $ticket->afterPhotos()->first(); @endphp
                            @if ($afterPhoto)
                                <div class="h-40 rounded-lg overflow-hidden bg-blue-950 flex items-center justify-center text-center p-4 border border-blue-500/30">
                                    <div class="text-cyan-300 text-xs font-bold tracking-wider drop-shadow-[0_0_8px_rgba(34,211,238,0.8)]">
                                        oo lenskart
                                        <div class="text-[10px] text-emerald-400 mt-1 font-normal">{{ $afterPhoto->caption ?? 'Repaired Signage' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="h-40 rounded-lg border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-400 text-xs text-center p-4">
                                    No After photograph uploaded yet.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upload Evidence Form -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <h4 class="font-bold text-xs text-slate-800 mb-2">Upload Photo Evidence</h4>
                        <form action="{{ route('tickets.upload-evidence', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Evidence Type</label>
                                    <select name="evidence_type" required class="w-full text-xs rounded-xl border-slate-300">
                                        <option value="before">Before Photo (Initial defect)</option>
                                        <option value="during">During Service (Internal wiring / parts)</option>
                                        <option value="after">After Photo (Rectification complete)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Caption / Note</label>
                                    <input type="text" name="caption" placeholder="e.g. New 24V driver installed" class="w-full text-xs rounded-xl border-slate-300" />
                                </div>
                            </div>
                            <div>
                                <input type="file" name="photo" accept="image/*" required class="w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700" />
                            </div>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800">
                                Upload Photo
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Right Col (1 col): Status Action Control Panel -->
            <div class="space-y-6">

                <!-- Status Update Panel -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 text-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Operation & Dispatch Control</h3>

                    <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Ticket Status</label>
                            <select name="status" class="w-full text-xs rounded-xl border-slate-300">
                                @foreach (['Open', 'Assigned', 'In Progress', 'Parts Required', 'Scheduled', 'Completed', 'Overdue', 'Cancelled'] as $st)
                                    <option value="{{ $st }}" {{ $ticket->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Assign Technician</label>
                            <select name="assigned_technician_id" class="w-full text-xs rounded-xl border-slate-300">
                                <option value="">-- Unassigned --</option>
                                @foreach ($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ $ticket->assigned_technician_id == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }} ({{ $tech->phone ?? 'Technician' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">ETA Display</label>
                            <input type="text" name="eta_display" value="{{ $ticket->eta_display }}" placeholder="e.g. Today, Tomorrow, 12 Dec" class="w-full text-xs rounded-xl border-slate-300" />
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Resolution Notes</label>
                            <textarea name="resolution_notes" rows="3" placeholder="Parts replaced, alignment checked, signed off..." class="w-full text-xs rounded-xl border-slate-300">{{ $ticket->resolution_notes }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-md shadow-blue-500/20">
                            Update Ticket
                        </button>
                    </form>
                </div>

                <!-- Location Info Card -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 text-xs">
                    <h4 class="font-bold text-slate-900 mb-2">Store Information</h4>
                    <p class="font-semibold text-slate-800">{{ $ticket->location->name ?? 'Nagpur' }}</p>
                    <p class="text-slate-500 text-[11px] mt-1">{{ $ticket->location->address ?? 'Nagpur, Maharashtra' }}</p>
                    <p class="text-slate-500 text-[11px] mt-1">Contact: {{ $ticket->location->primary_contact_number ?? '+91 8208900802' }}</p>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
