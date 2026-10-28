<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Downloads & Documentation Vault</h1>
                <p class="text-xs text-slate-500 mt-0.5">Export operational datasets, download company SOPs, service reports, and contracts</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 self-start sm:self-auto">
                Official Documents Vault
            </span>
        </div>

        <!-- Downloads Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($documents as $doc)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $doc['category'] }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc['format'] === 'PDF' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($doc['format'] === 'CSV' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                {{ $doc['format'] }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">{{ $doc['title'] }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $doc['description'] }}</p>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 font-medium">Verified System Resource</span>
                        <a href="{{ route($doc['download_route']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition-colors shadow-sm">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download {{ $doc['format'] }}</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>
