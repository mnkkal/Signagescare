<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto" x-data="{ showModal: false }">

        <!-- Page Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">My Locations & Branches</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage multi-location retail stores, assigned store managers, and covered signage facades</p>
            </div>
            <button @click="showModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Store Location</span>
            </button>
        </div>

        <!-- Locations Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($locations as $loc)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $loc->store_code }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 mt-1.5">{{ $loc->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $loc->city }}, {{ $loc->state }}</p>
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full {{ $loc->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                        </div>

                        <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 my-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-slate-400">Store Manager:</span>
                                <span class="font-bold text-slate-800">{{ $loc->storeManager->name ?? 'Store Manager' }}</span>
                            </div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-slate-400">Phone:</span>
                                <span class="font-medium text-slate-700">{{ $loc->primary_contact_number ?? '+91 8208900802' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Total Assets:</span>
                                <span class="font-bold text-blue-600">{{ $loc->assets->count() }} Assets</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('tickets.index', ['location_id' => $loc->id]) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                            View Tickets ({{ $loc->tickets->count() }}) &rarr;
                        </a>
                        <a href="{{ route('assets.index', ['location_id' => $loc->id]) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                            Asset Inventory &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Add Location Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Add New Store Location</h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('locations.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="font-semibold text-slate-700">Client / Brand</label>
                        <select name="client_id" required class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            @foreach ($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Store Name</label>
                        <input type="text" name="name" required placeholder="e.g. Lenskart - Wardha Road" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Store Code</label>
                        <input type="text" name="store_code" required placeholder="e.g. LKT-NAG-006" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700">City</label>
                            <input type="text" name="city" value="Nagpur" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">State</label>
                            <input type="text" name="state" value="Maharashtra" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Store Manager</label>
                        <select name="store_manager_user_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            <option value="">-- Select Store Manager --</option>
                            @foreach ($storeManagers as $sm)
                                <option value="{{ $sm->id }}">{{ $sm->name }} ({{ $sm->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Contact Number</label>
                        <input type="text" name="primary_contact_number" placeholder="+91 98200..." class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Save Location</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
