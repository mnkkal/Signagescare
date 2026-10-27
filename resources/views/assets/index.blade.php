<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Signage Asset Register</h1>
                <p class="text-xs text-slate-500 mt-0.5">Comprehensive audit, condition monitoring, and specifications of all registered signage assets</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    Total Assets: {{ $stats['total'] }}
                </span>
                <a href="{{ route('assets.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Register New Asset</span>
                </a>
            </div>
        </div>

        <!-- 3 Health Overview Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('assets.index', ['health_status' => 'healthy']) }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex items-center justify-between hover:border-emerald-300 transition-all">
                <div>
                    <div class="text-2xl font-black text-emerald-600">{{ $stats['healthy'] }}</div>
                    <div class="text-xs font-bold text-slate-800">Healthy Condition</div>
                    <div class="text-[11px] text-slate-500">Fully operational & illuminated</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    ✓
                </div>
            </a>

            <a href="{{ route('assets.index', ['health_status' => 'attention']) }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex items-center justify-between hover:border-amber-300 transition-all">
                <div>
                    <div class="text-2xl font-black text-amber-600">{{ $stats['attention'] }}</div>
                    <div class="text-xs font-bold text-slate-800">Attention Required</div>
                    <div class="text-[11px] text-slate-500">Minor flickering or dust ingress</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    !
                </div>
            </a>

            <a href="{{ route('assets.index', ['health_status' => 'critical']) }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex items-center justify-between hover:border-rose-300 transition-all">
                <div>
                    <div class="text-2xl font-black text-rose-600">{{ $stats['critical'] }}</div>
                    <div class="text-xs font-bold text-slate-800">Critical Failure</div>
                    <div class="text-[11px] text-slate-500">Blackout, power drop or structural issue</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    ✕
                </div>
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 flex items-center justify-between">
            <form action="{{ route('assets.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tag, name, sign type..." class="text-xs rounded-xl border-slate-200 w-52" />
                
                <select name="health_status" class="text-xs rounded-xl border-slate-200" onchange="this.form.submit()">
                    <option value="">All Health Statuses</option>
                    <option value="healthy" {{ request('health_status') === 'healthy' ? 'selected' : '' }}>Healthy</option>
                    <option value="attention" {{ request('health_status') === 'attention' ? 'selected' : '' }}>Attention</option>
                    <option value="critical" {{ request('health_status') === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>

                <select name="location_id" class="text-xs rounded-xl border-slate-200" onchange="this.form.submit()">
                    <option value="">All Locations</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-3 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold">Filter</button>
                @if (request()->hasAny(['search', 'health_status', 'location_id']))
                    <a href="{{ route('assets.index') }}" class="text-xs text-rose-600 hover:underline">Clear</a>
                @endif
            </form>
        </div>

        <!-- Assets Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Asset Tag</th>
                            <th class="py-3 px-4">Signage Name</th>
                            <th class="py-3 px-4">Location</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Dimensions</th>
                            <th class="py-3 px-4">Illumination</th>
                            <th class="py-3 px-4">Condition</th>
                            <th class="py-3 px-4 text-right">Update Health</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach ($assets as $asset)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 font-bold text-blue-600 whitespace-nowrap">
                                    {{ $asset->asset_tag }}
                                </td>
                                <td class="py-3 px-4 text-slate-900 font-semibold">
                                    {{ $asset->asset_name }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $asset->location->name ?? 'Nagpur' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                    {{ $asset->sign_type }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                    {{ $asset->dimensions ?? '-' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                    {{ $asset->illumination_type }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($asset->health_status === 'healthy')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Healthy
                                        </span>
                                    @elseif ($asset->health_status === 'attention')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Attention
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Critical
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <form action="{{ route('assets.update-health', $asset) }}" method="POST" class="inline-flex items-center gap-1.5">
                                        @csrf
                                        <select name="health_status" class="text-[10px] py-1 px-1.5 rounded-lg border-slate-300">
                                            <option value="healthy" {{ $asset->health_status === 'healthy' ? 'selected' : '' }}>Healthy</option>
                                            <option value="attention" {{ $asset->health_status === 'attention' ? 'selected' : '' }}>Attention</option>
                                            <option value="critical" {{ $asset->health_status === 'critical' ? 'selected' : '' }}>Critical</option>
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
                {{ $assets->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
