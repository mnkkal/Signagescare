<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="pb-4 border-b border-slate-200">
            <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:underline mb-2 inline-block">&larr; Back to Tickets</a>
            <h1 class="text-2xl font-bold text-slate-900">Raise a New Service Request</h1>
            <p class="text-xs text-slate-500 mt-0.5">Report breakdown, electrical defect, physical damage, or periodic maintenance requirement</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
            <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs">
                @csrf

                <!-- Store Location -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Store / Branch Location <span class="text-rose-500">*</span></label>
                    <select name="client_location_id" required class="w-full text-xs rounded-xl border-slate-300 focus:ring-blue-500">
                        <option value="">-- Choose Location --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->city }}) - {{ $loc->store_code }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Problem Category & Priority -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Problem Category <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full text-xs rounded-xl border-slate-300 focus:ring-blue-500">
                            <option value="Main Sign OFF">Main Sign OFF (Complete Blackout)</option>
                            <option value="2 Letters OFF">Partial Letters Dark / Module Out</option>
                            <option value="Flickering">Flickering / Fluctuating Illumination</option>
                            <option value="Sign Loose">Structural Instability / Sign Loose</option>
                            <option value="Cleaning">Surface Dirt / Acrylic Cleaning Reqd</option>
                            <option value="Power Supply Failure">Driver / SMPS Burnout</option>
                            <option value="Physical Damage">Storm / Impact Physical Damage</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Priority Level <span class="text-rose-500">*</span></label>
                        <select name="priority" required class="w-full text-xs rounded-xl border-slate-300 focus:ring-blue-500">
                            <option value="P1">P1 - Critical (Entire Facade Down / Structural Hazard)</option>
                            <option value="P2">P2 - High (Prominent Brand Letters Dark)</option>
                            <option value="P3" selected>P3 - Medium (Minor Flickering / Edge Lighting)</option>
                            <option value="P4">P4 - Low (Cleaning / Routine Alignment)</option>
                        </select>
                    </div>
                </div>

                <!-- Problem Title -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Issue Headline <span class="text-rose-500">*</span></label>
                    <input type="text" name="problem_title" required placeholder="e.g. Main front glowsign letters not turning on after 6 PM" class="w-full text-xs rounded-xl border-slate-300 focus:ring-blue-500" />
                </div>

                <!-- Detailed Description -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Detailed Description <span class="text-rose-500">*</span></label>
                    <textarea name="problem_description" rows="4" required placeholder="Describe what occurred, any strange noises, circuit breaker tripping, or timing of issue..." class="w-full text-xs rounded-xl border-slate-300 focus:ring-blue-500"></textarea>
                </div>

                <!-- Upload Photo -->
                <div class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-300">
                    <label class="block font-bold text-slate-700 mb-1">Upload Defect Photo (Initial Before Evidence)</label>
                    <p class="text-[11px] text-slate-500 mb-2">Capture a clear photograph showing the problem to speed up technician dispatch.</p>
                    <input type="file" name="issue_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('tickets.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 shadow-md shadow-blue-500/20">
                        Submit Service Request
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
