<x-app-layout>
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-200">
        <div>
            <a href="{{ route('assets.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">&larr; Back to Asset Register</a>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <h1 class="text-2xl font-bold text-slate-900">{{ $asset->asset_name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $asset->asset_tag }}</span>
                @if($asset->isRepeatFailure())
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                        Repeat-Failure Alert (&ge;3 tickets in 6 months)
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $asset->location->client->name ?? '' }} &mdash; {{ $asset->location->name ?? '' }} ({{ $asset->location->city ?? '' }})
            </p>
        </div>
        <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
            + Raise Ticket for This Asset
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Technical Specifications &amp; History</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold block">Signage Type</span>
                    <span class="font-bold text-slate-800">{{ $asset->sign_type }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Illumination</span>
                    <span class="font-bold text-slate-800">{{ $asset->illumination_type }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Dimensions</span>
                    <span class="font-bold text-slate-800">{{ $asset->dimensions ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Manufacturer / Model</span>
                    <span class="font-bold text-slate-800">{{ $asset->manufacturer_model ?? 'Not recorded' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Power Consumption</span>
                    <span class="font-bold text-slate-800">{{ $asset->power_rating_watts ? $asset->power_rating_watts . ' W' : 'Not recorded' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">GPS Coordinates</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $asset->gps_coordinates ?? 'Not recorded' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Installation Date</span>
                    <span class="font-bold text-slate-800">{{ $asset->installation_date?->format('d M Y') ?? 'Not recorded' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Warranty Expiry</span>
                    <span class="font-bold text-slate-800">{{ $asset->warranty_expiry_date?->format('d M Y') ?? 'Not recorded' }}</span>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-slate-400 font-semibold block">Linked Production Job Card / SOP</span>
                    @if($asset->sopProject)
                        <a href="{{ route('sop.show', $asset->sopProject) }}" class="font-bold text-blue-600 hover:underline">
                            {{ $asset->sopProject->title }} (Job #{{ $asset->sopProject->id }})
                        </a>
                    @else
                        <span class="text-slate-500">None linked</span>
                    @endif
                </div>
                @if($asset->notes)
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 font-semibold block">Notes</span>
                        <p class="text-slate-700 mt-0.5">{{ $asset->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Compliance &amp; Permits</h2>
            <form method="POST" action="{{ route('assets.upload-document', $asset) }}" enctype="multipart/form-data" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Document Type</label>
                    <select name="document_type" required class="w-full rounded-xl border-slate-300 text-xs">
                        <option value="Municipal Permit">Municipal Permit</option>
                        <option value="Electrical Test Certificate">Electrical Test Certificate</option>
                        <option value="Structural Stability">Structural Stability Certificate</option>
                        <option value="Insurance Policy">Insurance Policy</option>
                        <option value="Warranty Card">Warranty Card</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Title</label>
                    <input type="text" name="title" required placeholder="e.g. NMC Facade Permit 2026" class="w-full rounded-xl border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Expiry Date (Optional)</label>
                    <input type="date" name="expiry_date" class="w-full rounded-xl border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">File (PDF/Image, max 10MB)</label>
                    <input type="file" name="file" required class="w-full text-xs">
                </div>
                <button type="submit" class="w-full py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800">
                    Upload Compliance Document
                </button>
            </form>

            <div class="pt-3 border-t border-slate-100 space-y-2">
                @forelse($asset->documents as $doc)
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between gap-2 text-xs">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $doc->title }}</p>
                            <p class="text-[11px] text-slate-500">{{ $doc->document_type }} @if($doc->expiry_date) &bull; Exp: {{ $doc->expiry_date->format('d M Y') }} @endif</p>
                        </div>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 font-semibold hover:underline shrink-0">View</a>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic">No compliance documents uploaded.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Service Ticket History ({{ $asset->tickets->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Ticket #</th>
                        <th class="px-4 py-3 text-left font-semibold">Title</th>
                        <th class="px-4 py-3 text-left font-semibold">Priority</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Root Cause</th>
                        <th class="px-4 py-3 text-left font-semibold">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($asset->tickets as $ticket)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-bold text-blue-600">
                                <a href="{{ route('tickets.show', $ticket) }}" class="hover:underline">{{ $ticket->ticket_number }}</a>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $ticket->title }}</td>
                            <td class="px-4 py-3">{{ $ticket->priority }}</td>
                            <td class="px-4 py-3">{{ $ticket->status }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $ticket->root_cause ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $ticket->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">No tickets logged against this asset yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
