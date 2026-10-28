<x-app-layout>
    <div class="space-y-4 sm:space-y-6">

        <!-- ==================== HERO BANNER ==================== -->
        <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#07132c] via-[#0f2148] to-[#162e63] p-5 sm:p-8 text-white shadow-xl border border-blue-800/30">
            <!-- Ambient Glow -->
            <div class="absolute -right-10 -bottom-10 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-20 top-0 w-80 h-80 bg-cyan-400/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-cyan-300 text-xs font-semibold mb-3">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Signage Care Operations Hub
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white mb-2 leading-tight">
                    One Brand. Multiple Locations.<br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">
                        One Signage Care Partner.
                    </span>
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm mb-5 sm:mb-6 max-w-xl">
                    Centralized breakdown support, preventive maintenance, and visual evidence tracking across your retail signage network.
                </p>

                <!-- 4 Highlight Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <div class="bg-slate-900/50 backdrop-blur border border-blue-500/20 rounded-xl sm:rounded-2xl p-3 sm:p-3.5 flex sm:block items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-cyan-400 flex items-center justify-center sm:mb-2 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-white">Breakdown Support</div>
                            <div class="text-[11px] text-slate-400 leading-snug mt-0.5">Fast coordination when signage fails.</div>
                        </div>
                    </div>

                    <div class="bg-slate-900/50 backdrop-blur border border-blue-500/20 rounded-xl sm:rounded-2xl p-3 sm:p-3.5 flex sm:block items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-cyan-400 flex items-center justify-center sm:mb-2 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-white">Preventive Care</div>
                            <div class="text-[11px] text-slate-400 leading-snug mt-0.5">Scheduled checks before small issues grow.</div>
                        </div>
                    </div>

                    <div class="bg-slate-900/50 backdrop-blur border border-blue-500/20 rounded-xl sm:rounded-2xl p-3 sm:p-3.5 flex sm:block items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-cyan-400 flex items-center justify-center sm:mb-2 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-white">Multi-Location AMC</div>
                            <div class="text-[11px] text-slate-400 leading-snug mt-0.5">One service workflow across store network.</div>
                        </div>
                    </div>

                    <div class="bg-slate-900/50 backdrop-blur border border-blue-500/20 rounded-xl sm:rounded-2xl p-3 sm:p-3.5 flex sm:block items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-cyan-400 flex items-center justify-center sm:mb-2 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-white">Service Reporting</div>
                            <div class="text-[11px] text-slate-400 leading-snug mt-0.5">Ticket history with before-and-after proof.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== 4 KPI CARDS ==================== -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Open Tickets -->
            <a href="{{ route('tickets.index', ['status' => 'Open']) }}" class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-all group flex items-center justify-between">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 group-hover:text-blue-600 transition-colors">
                        {{ $openTicketsCount }}
                    </div>
                    <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-0.5 sm:mt-1">Open Tickets</div>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </a>

            <!-- In Progress -->
            <a href="{{ route('tickets.index', ['status' => 'In Progress']) }}" class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-all group flex items-center justify-between">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 group-hover:text-blue-600 transition-colors">
                        {{ $inProgressCount }}
                    </div>
                    <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-0.5 sm:mt-1">In Progress</div>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </a>

            <!-- Completed This Month -->
            <a href="{{ route('tickets.index', ['status' => 'Completed']) }}" class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-all group flex items-center justify-between">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 group-hover:text-emerald-600 transition-colors">
                        {{ $completedThisMonth }}
                    </div>
                    <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-0.5 sm:mt-1">Completed</div>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </a>

            <!-- Overdue -->
            <a href="{{ route('tickets.index', ['status' => 'Overdue']) }}" class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-5 shadow-sm border border-slate-200/80 hover:shadow-md transition-all group flex items-center justify-between">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-rose-600 group-hover:text-rose-700 transition-colors">
                        {{ $overdueCount }}
                    </div>
                    <div class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-0.5 sm:mt-1">Overdue</div>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-all shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </a>
        </div>

        <!-- ==================== MIDDLE ROW: ACTION BOX, ASSETS, HEALTH DONUT ==================== -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">

            <!-- Card 1: Report a Signage Problem -->
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-800">Report a Signage Problem</h3>
                            <p class="text-[11px] sm:text-xs text-slate-500">Fast-track breakdown support ticket</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 mb-4">
                        Upload a photo, select problem type and get a service ticket instantly with dedicated technician dispatch.
                    </p>
                </div>

                <a href="{{ route('tickets.create') }}"
                   class="w-full inline-flex items-center justify-center gap-2 py-2.5 sm:py-3 px-4 rounded-xl font-semibold text-xs text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 shadow-md shadow-blue-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Raise New Service Request</span>
                </a>
            </div>

            <!-- Card 2: Your Signage Assets -->
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Your Signage Assets</h3>
                    </div>
                    <a href="{{ route('assets.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                        View All <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="flex items-center justify-between my-2">
                    <div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-slate-900">{{ $totalAssets }}</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">Total Assets Under Care</div>
                    </div>
                    <div class="w-20 sm:w-24 h-12 sm:h-14 rounded-xl bg-slate-900 flex items-center justify-center text-cyan-400 font-black text-xs sm:text-sm tracking-wider shadow-inner border border-slate-700">
                        lenskart
                    </div>
                </div>

                <div class="text-[11px] text-slate-500 flex items-center gap-2 pt-2 border-t border-slate-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                    <span class="truncate">All stores covered under active AMC contract</span>
                </div>
            </div>

            <!-- Card 3: Asset Health Overview Donut Gauge -->
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-200/80 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Asset Health Overview</h3>
                    <a href="{{ route('assets.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                        View All <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="flex items-center justify-between gap-3 sm:gap-4 my-2">
                    <!-- Donut Chart Representation -->
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center flex-shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-emerald-500" stroke-dasharray="{{ $healthPercentage }}, 100" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute text-center">
                            <div class="text-sm sm:text-base font-extrabold text-slate-900">{{ $healthPercentage }}%</div>
                        </div>
                    </div>

                    <!-- Health Legend -->
                    <div class="flex-1 space-y-1 sm:space-y-1.5 text-xs">
                        <div class="text-xs font-bold text-slate-800">
                            {{ $healthyAssets }} / {{ $totalAssets }} <span class="font-normal text-slate-500">Healthy</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-600">
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Healthy</span>
                            <span class="font-bold text-slate-800">{{ $healthyAssets }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-600">
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Attention</span>
                            <span class="font-bold text-slate-800">{{ $attentionAssets }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-600">
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Critical</span>
                            <span class="font-bold text-slate-800">{{ $criticalAssets }}</span>
                        </div>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 pt-1 border-t border-slate-100 truncate">
                    Auto-computed from latest field inspection reports
                </div>
            </div>
        </div>

        <!-- ==================== BOTTOM ROW: RECENT TICKETS & SIDEBAR ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">

            <!-- Left Col (8 cols): Recent Service Tickets -->
            <div class="lg:col-span-8 bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">Recent Service Tickets</h3>
                        <p class="text-xs text-slate-500">Live breakdown requests and updates</p>
                    </div>
                    <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                        View All <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[580px]">
                        <thead class="bg-slate-50/75 text-slate-500 font-semibold uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">Ticket ID</th>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">Location</th>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">Problem</th>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">Priority</th>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">Status</th>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">Technician</th>
                                <th class="py-2.5 sm:py-3 px-3 sm:px-4">ETA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse ($recentTickets as $ticket)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-blue-600 whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="hover:underline">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700 whitespace-nowrap">
                                        {{ $ticket->location->name ?? 'General' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-800 font-semibold">
                                        {{ $ticket->problem_title }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $ticket->priority_badge_color }}">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $ticket->status_badge_color }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $ticket->technician->name ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap font-semibold">
                                        {{ $ticket->eta_display ?? 'Today' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400">No active tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Col (4 cols): Before & After + Preventive Maintenance -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Before & After Showcase -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-slate-900">Before & After</h3>
                        <a href="{{ route('tickets.index', ['status' => 'Completed']) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                            View All <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-2 relative rounded-xl overflow-hidden bg-slate-900 p-1 border border-slate-800">
                        <!-- Before Box -->
                        <div class="relative h-28 rounded-lg overflow-hidden bg-slate-800 flex items-center justify-center text-center p-2">
                            <div class="text-slate-400 text-[11px]">
                                <div class="font-bold text-slate-300">oo lenskart</div>
                                <div class="text-[9px] text-rose-400 mt-1">2 Letters Dark</div>
                            </div>
                            <span class="absolute bottom-1.5 left-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-900/80 text-white">
                                Before
                            </span>
                        </div>

                        <!-- After Box -->
                        <div class="relative h-28 rounded-lg overflow-hidden bg-blue-950 flex items-center justify-center text-center p-2 border border-blue-500/30">
                            <div class="text-cyan-300 text-[11px] font-bold tracking-wider drop-shadow-[0_0_8px_rgba(34,211,238,0.8)]">
                                oo lenskart
                                <div class="text-[9px] text-emerald-400 mt-1 font-normal">100% Repaired</div>
                            </div>
                            <span class="absolute bottom-1.5 right-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-600 text-white">
                                After
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Preventive Maintenance -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-slate-900">Scheduled Preventive Maintenance</h3>
                        <a href="{{ route('maintenance.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                            View All <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-[10px] text-slate-400 uppercase font-semibold border-b border-slate-100">
                                <tr>
                                    <th class="pb-2">Location</th>
                                    <th class="pb-2">Next Visit</th>
                                    <th class="pb-2">Last Visit</th>
                                    <th class="pb-2 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-[11px]">
                                @forelse ($maintenanceSchedules as $schedule)
                                    <tr>
                                        <td class="py-2.5 font-semibold text-slate-800">
                                            {{ $schedule->location->name ?? 'Nagpur' }}
                                        </td>
                                        <td class="py-2.5 text-slate-600 whitespace-nowrap">
                                            {{ $schedule->next_visit_date ? $schedule->next_visit_date->format('d M Y') : '12 Dec 2026' }}
                                        </td>
                                        <td class="py-2.5 text-slate-400 whitespace-nowrap">
                                            {{ $schedule->last_visit_date ? $schedule->last_visit_date->format('d M Y') : '12 Sep 2026' }}
                                        </td>
                                        <td class="py-2.5 text-right whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                                {{ $schedule->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-3 text-center text-slate-400">No scheduled visits.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
