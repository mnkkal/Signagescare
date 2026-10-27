<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Active Sessions & Security</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage browser sessions, active devices, and inspect security audit logs</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Session Driver: Database
                </span>
            </div>
        </div>

        <!-- Section 1: Active Browser Sessions -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Your Active Devices & Sessions</h2>
                    <p class="text-xs text-slate-500">If you notice any unfamiliar sessions, log out from other browser sessions immediately.</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach ($sessions as $session)
                    <div class="py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl {{ $session->is_current_device ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center flex-shrink-0">
                                @if (str_contains($session->platform, 'Mobile') || str_contains($session->platform, 'iPhone'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-800">{{ $session->platform }} &middot; {{ $session->browser }}</span>
                                    @if ($session->is_current_device)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            This Device
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    <span>IP: {{ $session->ip_address ?? '127.0.0.1' }}</span> &middot;
                                    <span>Last Active: {{ $session->last_active }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            @if (!$session->is_current_device)
                                <form action="{{ route('sessions.destroy', $session->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700 px-3 py-1.5 rounded-lg hover:bg-rose-50 border border-rose-200 transition-colors">
                                        Terminate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Log Out Other Sessions Action Form -->
            <div class="mt-6 pt-6 border-t border-slate-100" x-data="{ open: false }">
                <button @click="open = !open" class="px-4 py-2.5 rounded-xl font-semibold text-xs text-white bg-slate-900 hover:bg-slate-800 transition-colors">
                    Log Out Other Browser Sessions
                </button>

                <div x-show="open" x-cloak class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 max-w-md">
                    <form action="{{ route('sessions.destroy-others') }}" method="POST" class="space-y-3">
                        @csrf
                        <p class="text-xs text-slate-600">Please enter your password to confirm logging out of your other browser sessions across all devices.</p>
                        <div>
                            <input type="password" name="password" placeholder="Enter your current password" class="w-full text-xs rounded-lg border-slate-300 focus:ring-blue-500" required />
                            @error('password')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-600 text-white hover:bg-rose-700">
                                Confirm Revoke
                            </button>
                            <button type="button" @click="open = false" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-200">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Section 2: Security & Audit Trails -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Security Audit Logs</h2>
                    <p class="text-xs text-slate-500">Immutable trace of authentication, permissions and sensitive system actions</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Timestamp</th>
                            <th class="py-3 px-4">Action</th>
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">IP Address</th>
                            <th class="py-3 px-4">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse ($recentLogs as $log)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500 text-[11px]">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-semibold text-slate-800">
                                    {{ $log->user->name ?? 'System' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                                <td class="py-3 px-4 text-slate-700">
                                    {{ $log->description ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">No audit log entries recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
