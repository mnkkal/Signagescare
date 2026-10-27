<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between pb-4 border-b border-slate-200 gap-4">
            <div>
                <a href="{{ route('amc.index') }}" class="text-xs font-semibold text-blue-600 hover:underline mb-1 inline-block">&larr; Back to AMC Contracts</a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $amc->contract_number }}: {{ $amc->title }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ ucfirst($amc->status) }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Client: <span class="font-bold text-slate-800">{{ $amc->client->name }}</span> &middot; SLA: <span class="text-blue-600 font-bold">{{ $amc->sla_response_hours }}h Response / {{ $amc->sla_resolution_hours }}h Fix</span></p>
            </div>
            <div class="text-right">
                <div class="text-xs text-slate-400 font-medium">Contract Value</div>
                <div class="text-2xl font-black text-slate-900">₹{{ number_format($amc->contract_value, 2) }}</div>
            </div>
        </div>

        <!-- 4 Terms Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-[11px] text-slate-400 font-semibold uppercase">Effective Period</div>
                <div class="text-xs font-bold text-slate-900 mt-1">{{ $amc->start_date ? $amc->start_date->format('d M Y') : '-' }} &rarr; {{ $amc->end_date ? $amc->end_date->format('d M Y') : '-' }}</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-[11px] text-slate-400 font-semibold uppercase">Visit Frequency</div>
                <div class="text-xs font-bold text-slate-900 mt-1">{{ $amc->visit_frequency }} Inspection</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-[11px] text-slate-400 font-semibold uppercase">Stores Covered</div>
                <div class="text-xs font-bold text-blue-600 mt-1">{{ $amc->locations->count() }} Retail Branches</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-[11px] text-slate-400 font-semibold uppercase">Response SLA</div>
                <div class="text-xs font-bold text-emerald-600 mt-1">{{ $amc->sla_response_hours }} Hours Guaranteed</div>
            </div>
        </div>

        <!-- Scope & Terms -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 mb-2">Coverage Scope & Terms</h3>
            <p class="text-xs text-slate-700 leading-relaxed">{{ $amc->coverage_scope }}</p>
        </div>

        <!-- Covered Stores & Their Assets -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Covered Stores & Facades</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($amc->locations as $loc)
                    <div class="p-4 hover:bg-slate-50 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-xs text-slate-900">{{ $loc->name }} ({{ $loc->store_code }})</div>
                            <div class="text-[11px] text-slate-500">{{ $loc->city }} &middot; {{ $loc->assets->count() }} assets monitored</div>
                        </div>
                        <a href="{{ route('locations.show', $loc) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                            View Store Details &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-app-layout>
