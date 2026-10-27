<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">

        <div class="pb-4 border-b border-slate-200">
            <a href="{{ route('assets.index') }}" class="text-xs font-semibold text-blue-600 hover:underline mb-1 inline-block">&larr; Back to Asset Register</a>
            <h1 class="text-2xl font-bold text-slate-900">Register New Signage Asset</h1>
            <p class="text-xs text-slate-500 mt-0.5">Add signage facade, blade sign, pylon, or letter set to the monitoring registry</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
            <form action="{{ route('assets.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Store / Branch Location <span class="text-rose-500">*</span></label>
                    <select name="client_location_id" required class="w-full text-xs rounded-xl border-slate-300">
                        <option value="">-- Choose Store Location --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}) - {{ $loc->store_code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Asset Tag / ID <span class="text-rose-500">*</span></label>
                        <input type="text" name="asset_tag" required placeholder="e.g. AST-LKT-048" class="w-full text-xs rounded-xl border-slate-300" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Signage Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="asset_name" required placeholder="e.g. Main Front Fascia Acrylic 3D LED" class="w-full text-xs rounded-xl border-slate-300" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Signage Type <span class="text-rose-500">*</span></label>
                        <select name="sign_type" required class="w-full text-xs rounded-xl border-slate-300">
                            <option value="Acrylic 3D LED">Acrylic 3D LED</option>
                            <option value="ACP Backlit">ACP Backlit</option>
                            <option value="Glowsign LED">Glowsign LED</option>
                            <option value="Vertical Blade Sign">Vertical Blade Sign</option>
                            <option value="Pylon Sign">Pylon Sign</option>
                            <option value="Flex Board">Flex Board</option>
                            <option value="Neon LED Flex">Neon LED Flex</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Dimensions (W x H x D)</label>
                        <input type="text" name="dimensions" placeholder="e.g. 24ft x 4ft x 6in" class="w-full text-xs rounded-xl border-slate-300" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Illumination Type <span class="text-rose-500">*</span></label>
                        <select name="illumination_type" required class="w-full text-xs rounded-xl border-slate-300">
                            <option value="Backlit LED">Backlit LED</option>
                            <option value="Frontlit LED">Frontlit LED</option>
                            <option value="Edge-lit">Edge-lit</option>
                            <option value="Non-lit">Non-lit</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Initial Health Status <span class="text-rose-500">*</span></label>
                        <select name="health_status" required class="w-full text-xs rounded-xl border-slate-300">
                            <option value="healthy" selected>Healthy (Fully functional)</option>
                            <option value="attention">Attention Required (Minor flaw)</option>
                            <option value="critical">Critical (Non-functional)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Technical Notes / Specifications</label>
                    <textarea name="notes" rows="3" placeholder="Power supply wattage, LED brand, wiring route, mounting notes..." class="w-full text-xs rounded-xl border-slate-300"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('assets.index') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-md shadow-blue-500/20">
                        Save Signage Asset
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
