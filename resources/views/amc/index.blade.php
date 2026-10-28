<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Annual Maintenance Contracts (AMC)</h1>
                <p class="text-xs text-slate-500 mt-0.5">Multi-location retail signage maintenance agreements, SLAs, and coverage scope</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 self-start sm:self-auto">
                Active AMC Status
            </span>
        </div>

        <div class="space-y-6">
            @foreach ($contracts as $contract)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                    <div class="flex flex-wrap items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-0.5 rounded text-xs font-black bg-slate-900 text-cyan-300">
                                    {{ $contract->contract_number }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Active Contract
                                </span>
                            </div>
                            <h2 class="text-lg font-bold text-slate-900 mt-2">{{ $contract->title }}</h2>
                            <p class="text-xs text-slate-500">Client: <span class="font-bold text-slate-700">{{ $contract->client->name }}</span></p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-400 font-medium">Contract Value</div>
                            <div class="text-xl font-black text-slate-900">₹{{ number_format($contract->contract_value, 2) }}</div>
                        </div>
                    </div>

                    <!-- SLA & Schedule Badges -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-4">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Start Date</div>
                            <div class="text-xs font-bold text-slate-800">{{ $contract->start_date ? $contract->start_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Valid Until</div>
                            <div class="text-xs font-bold text-slate-800">{{ $contract->end_date ? $contract->end_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Visit Frequency</div>
                            <div class="text-xs font-bold text-slate-800">{{ $contract->visit_frequency }}</div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">SLA Guarantee</div>
                            <div class="text-xs font-bold text-blue-600">{{ $contract->sla_response_hours }}h Response / {{ $contract->sla_resolution_hours }}h Fix</div>
                        </div>
                    </div>

                    <!-- Scope of Work -->
                    <div class="text-xs text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100 mb-4">
                        <span class="font-bold text-slate-900 block mb-1">Coverage Scope:</span>
                        <p>{{ $contract->coverage_scope ?? 'Full multi-location facade lighting, LED module replacement, power supply driver maintenance, quarterly ultrasonic cleaning.' }}</p>
                    </div>

                    <!-- Covered Store Locations -->
                    <div>
                        <h4 class="font-bold text-xs text-slate-800 mb-2">Covered Retail Locations ({{ $contract->locations->count() }})</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($contract->locations as $cl)
                                <span class="px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                    {{ $cl->name }} ({{ $cl->city }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>
