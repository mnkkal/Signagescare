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
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $ticket->billing_type ?? 'AMC Included' }}
                    </span>
                    @if($ticket->escalation_level > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                            SLA Escalation L{{ $ticket->escalation_level }}
                        </span>
                    @endif
                    @if($ticket->asset && $ticket->asset->isRepeatFailure())
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            Repeat-Failure Asset
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Location: <span class="font-bold text-slate-800">{{ $ticket->location->name ?? 'General' }}</span> &middot; Raised: {{ $ticket->created_at->format('d M Y, h:i A') }}
                    @if($ticket->sla_due_at) &middot; Resp SLA: <span class="font-semibold">{{ $ticket->sla_due_at->format('d M, h:i A') }}</span> @endif
                    @if($ticket->resolution_sla_due_at) &middot; Res SLA: <span class="font-semibold">{{ $ticket->resolution_sla_due_at->format('d M, h:i A') }}</span> @endif
                </p>
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

                <!-- Field Visit Check-In / Check-Out -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Technician Site Check-In &amp; Travel</h3>
                            <p class="text-xs text-slate-500">Arrival/departure timestamps, GPS coordinates, and vehicle expense</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <form action="{{ route('tickets.check-in', $ticket) }}" method="POST" data-offline-sync class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5">
                            @csrf
                            <div class="font-bold text-slate-800">Site Check-In</div>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="lat" id="checkin_lat" placeholder="Latitude" class="rounded-lg border-slate-300 text-xs">
                                <input type="text" name="lng" id="checkin_lng" placeholder="Longitude" class="rounded-lg border-slate-300 text-xs">
                            </div>
                            <input type="text" name="visit_notes" placeholder="Arrival note (optional)" class="w-full rounded-lg border-slate-300 text-xs">
                            <div class="flex gap-2">
                                <button type="button" onclick="if(navigator.geolocation){navigator.geolocation.getCurrentPosition(p=>{document.getElementById('checkin_lat').value=p.coords.latitude.toFixed(6);document.getElementById('checkin_lng').value=p.coords.longitude.toFixed(6);})}" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 font-semibold hover:bg-slate-100">
                                    Get GPS
                                </button>
                                <button type="submit" class="flex-1 py-1.5 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                                    Check-In Now
                                </button>
                            </div>
                        </form>

                        <form action="{{ route('tickets.check-out', $ticket) }}" method="POST" data-offline-sync class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5">
                            @csrf
                            <div class="font-bold text-slate-800">Site Check-Out</div>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" step="0.1" min="0" name="travel_distance_km" placeholder="Travel (km)" class="rounded-lg border-slate-300 text-xs">
                                <input type="number" step="0.01" min="0" name="vehicle_cost" placeholder="Vehicle Cost (₹)" class="rounded-lg border-slate-300 text-xs">
                            </div>
                            <input type="text" name="visit_notes" placeholder="Departure / site remarks" class="w-full rounded-lg border-slate-300 text-xs">
                            <button type="submit" class="w-full py-1.5 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800">
                                Check-Out &amp; Save Travel
                            </button>
                        </form>
                    </div>

                    @if($ticket->visits->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-xs">
                                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Technician</th>
                                        <th class="px-3 py-2 text-left">Check-In</th>
                                        <th class="px-3 py-2 text-left">Check-Out</th>
                                        <th class="px-3 py-2 text-left">Travel / Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($ticket->visits as $visit)
                                        <tr>
                                            <td class="px-3 py-2 font-semibold text-slate-800">{{ $visit->technician->name ?? 'Technician' }}</td>
                                            <td class="px-3 py-2 text-slate-600">{{ $visit->checked_in_at?->format('d M, H:i') }} @if($visit->checkin_lat) <span class="text-[10px] text-slate-400">({{ $visit->checkin_lat }}, {{ $visit->checkin_lng }})</span> @endif</td>
                                            <td class="px-3 py-2 text-slate-600">{{ $visit->checked_out_at?->format('d M, H:i') ?? 'On Site' }}</td>
                                            <td class="px-3 py-2 text-slate-600">{{ $visit->travel_distance_km ? $visit->travel_distance_km.' km' : '-' }} / {{ $visit->vehicle_cost ? '₹'.number_format($visit->vehicle_cost, 2) : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Pre-Work Safety & Compliance Checklist -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Pre-Work Safety &amp; Isolation Checklist</h3>
                            <p class="text-xs text-slate-500">Mandatory before changing ticket status to In Progress</p>
                        </div>
                        @if($ticket->safetyChecklists->whereNotNull('verified_at')->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Safety Verified</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending Verification</span>
                        @endif
                    </div>

                    <form action="{{ route('tickets.safety', $ticket) }}" method="POST" data-offline-sync class="space-y-3 text-xs p-4 rounded-xl bg-slate-50 border border-slate-200">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="inline-flex items-center gap-2 font-semibold text-slate-800">
                                <input type="checkbox" name="ppe_verified" value="1" required class="rounded border-slate-300 text-emerald-600">
                                PPE Kit Verified (Helmet, Insulated Gloves, Harness)
                            </label>
                            <label class="inline-flex items-center gap-2 font-semibold text-slate-800">
                                <input type="checkbox" name="electrical_isolation_confirmed" value="1" required class="rounded border-slate-300 text-emerald-600">
                                Electrical Lockout / Isolation Confirmed (MCB Off)
                            </label>
                            <label class="inline-flex items-center gap-2 font-semibold text-slate-800">
                                <input type="checkbox" name="work_at_height_required" value="1" class="rounded border-slate-300 text-amber-600">
                                Work-at-Height Required (&gt;6 ft / Facade / Rooftop)
                            </label>
                            <label class="inline-flex items-center gap-2 font-semibold text-slate-800">
                                <input type="checkbox" name="work_at_height_permit" value="1" class="rounded border-slate-300 text-amber-600">
                                Work-at-Height Permit Approved
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input type="text" name="permit_number" placeholder="Permit # (if height work)" class="rounded-lg border-slate-300 text-xs">
                            <input type="text" name="safety_notes" placeholder="Site hazard / ladder / scaffolding notes" class="rounded-lg border-slate-300 text-xs">
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                            Verify &amp; Submit Safety Checklist
                        </button>
                    </form>
                </div>

                <!-- Spare Parts Consumed -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <h3 class="text-base font-bold text-slate-900">Spare Parts Consumed</h3>
                    <form action="{{ route('tickets.attach-part', $ticket) }}" method="POST" class="flex flex-wrap items-end gap-3 text-xs">
                        @csrf
                        <div class="flex-1 min-w-[200px]">
                            <label class="block font-bold text-slate-700 mb-1">Spare Part</label>
                            <select name="spare_part_id" required class="w-full rounded-xl border-slate-300 text-xs">
                                <option value="">-- Select Part from Inventory --</option>
                                @foreach($spareParts as $part)
                                    <option value="{{ $part->id }}">{{ $part->name }} ({{ $part->sku }}) &mdash; Stock: {{ $part->current_stock }} {{ $part->unit }} @ ₹{{ number_format($part->unit_cost, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-24">
                            <label class="block font-bold text-slate-700 mb-1">Qty</label>
                            <input type="number" name="quantity" min="1" value="1" required class="w-full rounded-xl border-slate-300 text-xs">
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800">
                            Record Part Used
                        </button>
                    </form>

                    @if($ticket->parts->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-xs">
                                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Part</th>
                                        <th class="px-3 py-2 text-left">Qty</th>
                                        <th class="px-3 py-2 text-left">Unit Cost</th>
                                        <th class="px-3 py-2 text-left">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($ticket->parts as $tp)
                                        <tr>
                                            <td class="px-3 py-2 font-semibold text-slate-800">{{ $tp->sparePart->name ?? 'Part' }}</td>
                                            <td class="px-3 py-2">{{ $tp->quantity }} {{ $tp->sparePart->unit ?? '' }}</td>
                                            <td class="px-3 py-2">₹{{ number_format($tp->unit_cost, 2) }}</td>
                                            <td class="px-3 py-2 font-bold">₹{{ number_format($tp->total_cost, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Billing, Quotations & Client Feedback -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-base font-bold text-slate-900">Quotations, Invoices &amp; Client Feedback</h3>
                        <a href="{{ route('billing.index', ['ticket_id' => $ticket->id]) }}" class="text-xs font-semibold text-blue-600 hover:underline">Manage in Billing &rarr;</a>
                    </div>

                    @if($ticket->quotations->isNotEmpty() || $ticket->invoices->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            @foreach($ticket->quotations as $q)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                    <div class="font-bold text-slate-800">{{ $q->quotation_number }} &bull; ₹{{ number_format($q->total_amount, 2) }}</div>
                                    <div class="text-[11px] text-slate-500">Status: {{ ucfirst($q->status) }}</div>
                                </div>
                            @endforeach
                            @foreach($ticket->invoices as $inv)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $inv->invoice_number }} &bull; ₹{{ number_format($inv->total_amount, 2) }}</div>
                                        <div class="text-[11px] text-slate-500">Status: {{ ucfirst($inv->payment_status ?? 'Unpaid') }}</div>
                                    </div>
                                    <a href="{{ route('billing.invoices.show', $inv) }}" class="text-blue-600 font-semibold hover:underline">GST Invoice</a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($ticket->status === 'Completed')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-100 text-xs">
                            <form action="{{ route('tickets.rate', $ticket) }}" method="POST" class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 space-y-2.5">
                                @csrf
                                <div class="font-bold text-amber-900">Client Service Rating (1–5 Stars)</div>
                                <select name="client_rating" required class="w-full rounded-lg border-amber-300 text-xs">
                                    @for($r = 5; $r >= 1; $r--)
                                        <option value="{{ $r }}" {{ $ticket->client_rating == $r ? 'selected' : '' }}>{{ $r }} Star{{ $r > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                <input type="text" name="client_feedback" value="{{ $ticket->client_feedback }}" placeholder="Feedback on repair quality..." class="w-full rounded-lg border-amber-300 text-xs">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-600 text-white font-semibold hover:bg-amber-700">
                                    Submit Rating
                                </button>
                            </form>

                            <form action="{{ route('tickets.reopen', $ticket) }}" method="POST" class="p-4 rounded-xl bg-rose-50/70 border border-rose-200 space-y-2.5">
                                @csrf
                                <div class="font-bold text-rose-900">Issue Not Fixed? Reopen / Reject</div>
                                <input type="text" name="reopen_reason" required minlength="5" placeholder="Describe why the fix did not hold..." class="w-full rounded-lg border-rose-300 text-xs">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white font-semibold hover:bg-rose-700">
                                    Reopen Ticket
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                @if($ticket->notifications->isNotEmpty())
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-3">
                        <h3 class="text-base font-bold text-slate-900">Customer Alert Log</h3>
                        <ul class="divide-y divide-slate-100 text-xs">
                            @foreach($ticket->notifications as $notif)
                                <li class="py-2 flex items-center justify-between gap-2">
                                    <span><strong class="uppercase text-blue-700">{{ $notif->channel }}</strong> ({{ $notif->event }}): {{ $notif->message }}</span>
                                    <span class="text-slate-400 shrink-0">{{ $notif->created_at?->format('d M, H:i') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>

            <!-- Right Col (1 col): Status Action Control Panel -->
            <div class="space-y-6">

                <!-- Status Update Panel -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 text-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Operation & Dispatch Control</h3>

                    <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" data-offline-sync class="space-y-4">
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
                            <label class="block font-bold text-slate-700 mb-1">Billing Classification</label>
                            <select name="billing_type" class="w-full text-xs rounded-xl border-slate-300">
                                @foreach (['AMC Covered', 'Under Warranty', 'Chargeable'] as $bt)
                                    <option value="{{ $bt }}" {{ ($ticket->billing_type ?? 'AMC Covered') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Root Cause (Required to Complete)</label>
                            <select name="root_cause" class="w-full text-xs rounded-xl border-slate-300">
                                <option value="">-- Select Root Cause --</option>
                                @foreach (['SMPS / Driver Failure', 'LED Module Burnout', 'Loose Wiring / Water Ingress', 'Physical / Storm Damage', 'Timer / MCB Fault', 'Acrylic / ACP Structural Wear', 'Routine Cleaning'] as $rc)
                                    <option value="{{ $rc }}" {{ $ticket->root_cause === $rc ? 'selected' : '' }}>{{ $rc }}</option>
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
                    <p class="text-slate-500 text-[11px] mt-1">Contact: {{ $ticket->location->primary_contact_number ?? \App\Models\Setting::getValue('support_phone', '8208900802') }}</p>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
