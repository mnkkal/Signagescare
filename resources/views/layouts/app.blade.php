<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Signage Care CRM') }} - Multi-Location Operations</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased text-slate-800 bg-[#f1f5f9]">
    <div class="flex h-screen overflow-hidden">

        <!-- ==================== SIDEBAR ==================== -->
        <aside class="w-64 bg-gradient-to-b from-[#091124] via-[#0d1b3e] to-[#080d1a] text-slate-300 flex flex-col flex-shrink-0 border-r border-blue-900/30 shadow-2xl z-30">
            <!-- Brand Logo -->
            <div class="px-5 pt-5 pb-4 border-b border-blue-900/40">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-black tracking-wider text-white flex items-center gap-1">
                            SIGNAGE<span class="text-blue-400 font-extrabold">CARE</span>
                        </div>
                        <div class="text-[9px] tracking-widest uppercase text-cyan-300/80 font-semibold">
                            YOUR SIGN &middot; OUR CARE
                        </div>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['route' => 'tickets.create', 'label' => 'Service Request', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ['route' => 'locations.index', 'label' => 'My Locations', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['route' => 'tickets.index', 'label' => 'Service Tickets', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['route' => 'maintenance.index', 'label' => 'Preventive Maintenance', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['route' => 'assets.index', 'label' => 'Asset Register', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                        ['route' => 'amc.index', 'label' => 'AMC Details', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        ['route' => 'sop.index', 'label' => 'Operations & SOP', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['route' => 'users.index', 'label' => 'User & Roles', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                        ['route' => 'sessions.index', 'label' => 'Sessions & Security', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                        ['route' => 'reports.index', 'label' => 'Reports', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'],
                        ['route' => 'downloads.index', 'label' => 'Downloads', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
                        ['route' => 'support', 'label' => 'Support Desk', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php $isActive = request()->routeIs($item['route'].'*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ $isActive ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-500/25 font-semibold' : 'text-slate-300/80 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Sidebar Footer Contact & Guarantee -->
            <div class="p-4 mx-3 mb-3 rounded-2xl bg-gradient-to-br from-blue-950/70 to-slate-900/90 border border-blue-500/20 text-xs">
                <div class="flex items-center gap-2 mb-2 text-blue-400 font-semibold">
                    <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                    </svg>
                    <span>Timely Service Guaranteed</span>
                </div>
                <div class="space-y-1 text-slate-300">
                    <div class="flex items-center gap-1.5 font-medium">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>8208900802</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <span>Signagecare.in</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ==================== MAIN CONTENT AREA ==================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- TOP NAVBAR -->
            <header class="bg-gradient-to-r from-[#0d162d] via-[#101d3b] to-[#0a1224] text-white border-b border-blue-900/30 px-6 py-3.5 flex items-center justify-between sticky top-0 z-20 shadow-md">
                <!-- Global Search -->
                <form action="{{ route('tickets.index') }}" method="GET" class="w-full max-w-lg">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by Ticket ID, Location or Asset..."
                               class="w-full pl-10 pr-4 py-2 bg-slate-900/60 border border-blue-900/40 rounded-full text-xs text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                    </div>
                </form>

                <!-- Right Controls: Notification & User Profile -->
                <div class="flex items-center gap-4">
                    <!-- Notification Bell with counter -->
                    <a href="{{ route('tickets.index', ['status' => 'Open']) }}" class="relative p-2 text-slate-300 hover:text-white rounded-full hover:bg-slate-800/60 transition-colors" title="Pending Tickets">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center ring-2 ring-slate-900">
                            3
                        </span>
                    </a>

                    <!-- Current User Pill (Store Manager - Lenskart Nagpur) -->
                    <div class="relative flex items-center gap-3 pl-3 border-l border-slate-700/60" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2.5 text-left focus:outline-none group">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center text-white font-bold text-xs ring-2 ring-blue-400/40 shadow-sm flex-shrink-0">
                                {{ strtoupper(substr(Auth::user()->name ?? 'SK', 0, 2)) }}
                            </div>
                            <div class="hidden md:block">
                                <div class="text-xs font-semibold text-white group-hover:text-blue-300 transition-colors leading-tight">
                                    {{ Auth::user()->name ?? 'Store Manager' }}
                                </div>
                                <div class="text-[11px] text-slate-400 leading-tight">
                                    {{ Auth::user()->client->name ?? 'Lenskart Nagpur' }}
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-cloak
                             class="absolute right-0 top-12 w-56 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl py-2 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-slate-800">
                                <p class="text-white font-semibold">{{ Auth::user()->name }}</p>
                                <p class="text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-blue-950 text-blue-300 border border-blue-800">
                                    {{ Auth::user()->getRoleNames()->first() ?? 'Authorized User' }}
                                </span>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">Profile Settings</a>
                            <a href="{{ route('sessions.index') }}" class="block px-4 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">Active Sessions & Devices</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-800 mt-1">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-rose-400 hover:bg-slate-800">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- FLASH ALERTS -->
            @if (session('status'))
                <div class="mx-6 mt-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-6 mt-4 p-3 bg-rose-500/10 border border-rose-500/30 text-rose-800 rounded-xl text-xs font-medium flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- PAGE CONTENT INJECTION -->
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
