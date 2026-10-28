<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto" x-data="{ showCreateModal: false }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">User & Role Management</h1>
                <p class="text-xs text-slate-500 mt-0.5">Control role-based access control (RBAC), user privileges, and branch assignments</p>
            </div>
            <button @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add New User</span>
            </button>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-4">
                <form action="{{ route('users.index') }}" method="GET" class="flex items-center gap-3 w-full max-w-md">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..." class="w-full text-xs rounded-xl border-slate-200 focus:ring-blue-500" />
                    <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">Filter</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[640px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Affiliated Brand</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Last Login</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach ($users as $u)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $u->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $u->getRoleNames()->first() ?? 'None' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                    {{ $u->client->name ?? 'Internal Team' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $u->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ ucfirst($u->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-400 text-[11px]">
                                    {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-right">
                                    <form action="{{ route('users.update-role', $u) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <select name="role" class="text-[11px] py-1 px-2 rounded-lg border-slate-300">
                                            @foreach ($roles as $r)
                                                <option value="{{ $r->name }}" {{ $u->hasRole($r->name) ? 'selected' : '' }}>{{ $r->name }}</option>
                                            @endforeach
                                        </select>
                                        <select name="status" class="text-[11px] py-1 px-2 rounded-lg border-slate-300">
                                            <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="suspended" {{ $u->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        </select>
                                        <button type="submit" class="px-2.5 py-1 text-xs font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800">
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
                {{ $users->links() }}
            </div>
        </div>

        <!-- Add User Modal -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showCreateModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Add New User</h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('users.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="font-semibold text-slate-700">Full Name</label>
                        <input type="text" name="name" required class="w-full mt-1 text-xs rounded-xl border-slate-200" placeholder="e.g. Rahul Sharma" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Email Address</label>
                        <input type="email" name="email" required class="w-full mt-1 text-xs rounded-xl border-slate-200" placeholder="user@company.com" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Phone Number</label>
                        <input type="text" name="phone" class="w-full mt-1 text-xs rounded-xl border-slate-200" placeholder="+91 98200..." />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Role</label>
                        <select name="role" required class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            @foreach ($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Client / Brand Affiliation (Optional)</label>
                        <select name="client_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            <option value="">None (Internal Team)</option>
                            @foreach ($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Temporary Password</label>
                        <input type="password" name="password" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showCreateModal = false" class="px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Create User</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
