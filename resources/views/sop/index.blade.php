<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Signage Operations & SOP Engine</h1>
                <p class="text-xs text-slate-500 mt-0.5">Standard Operating Procedure pipeline: Sales &rarr; Recce &rarr; Design &rarr; Fabrication &rarr; Production &rarr; Dispatch &rarr; Installation &rarr; Handover</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 self-start sm:self-auto">
                10-Stage SOP Standard
            </span>
        </div>

        <!-- SOP Pipeline Overview -->
        <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white rounded-2xl p-6 shadow-md border border-blue-900/40">
            <h2 class="text-sm font-bold text-cyan-300 mb-3">Live SOP Operational Stages</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2 text-center text-xs">
                @php
                    $stagesList = [
                        ['num' => '1', 'name' => 'Sales / Req'],
                        ['num' => '2', 'name' => 'Site Recce'],
                        ['num' => '3', 'name' => 'Creative & Jobcard'],
                        ['num' => '4', 'name' => 'Fabrication'],
                        ['num' => '5', 'name' => 'Production'],
                        ['num' => '6', 'name' => 'Packing & Dispatch'],
                        ['num' => '7', 'name' => 'Installation'],
                        ['num' => '8', 'name' => 'Handover & Audit'],
                    ];
                @endphp
                @foreach ($stagesList as $stage)
                    <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700/60">
                        <div class="w-6 h-6 rounded-full bg-blue-500 text-white font-bold text-[10px] mx-auto flex items-center justify-center mb-1.5">
                            {{ $stage['num'] }}
                        </div>
                        <div class="font-semibold text-[11px] text-slate-200">{{ $stage['name'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Projects Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Active Signage SOP Projects</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[640px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Project Code</th>
                            <th class="py-3 px-4">Project Title</th>
                            <th class="py-3 px-4">Client Location</th>
                            <th class="py-3 px-4">Current Stage</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Verified Items</th>
                            <th class="py-3 px-4 text-right">View Checklist</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach ($projects as $prj)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3.5 px-4 font-bold text-blue-600 whitespace-nowrap">
                                    <a href="{{ route('sop.show', $prj) }}" class="hover:underline">
                                        {{ $prj->project_code }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $prj->title }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $prj->location->name ?? 'Nagpur' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ ucwords(str_replace('_', ' ', $prj->current_stage)) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $prj->overall_status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $prj->checklists->where('status', 'yes')->count() }} / {{ $prj->checklists->count() }} items signed off
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('sop.show', $prj) }}" class="px-3 py-1.5 text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 rounded-lg transition-colors">
                                        Open SOP &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
