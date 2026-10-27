<x-app-layout>
    <div class="space-y-6 max-w-6xl mx-auto">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between pb-4 border-b border-slate-200 gap-4">
            <div>
                <a href="{{ route('sop.index') }}" class="text-xs font-semibold text-blue-600 hover:underline mb-1 inline-block">&larr; Back to SOP Projects</a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $project->project_code }}: {{ $project->title }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        {{ $project->overall_status }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Location: <span class="font-bold text-slate-800">{{ $project->location?->name ?? 'Nagpur' }}</span> &middot; Client: <span class="font-bold text-slate-800">{{ $project->client?->name ?? 'Client Partner' }}</span></p>
            </div>

            <!-- Advance Stage Form -->
            <form action="{{ route('sop.advance-stage', $project) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="current_stage" class="text-xs rounded-xl border-slate-300">
                    @foreach ($stages as $key => $label)
                        <option value="{{ $key }}" {{ $project->current_stage === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="overall_status" class="text-xs rounded-xl border-slate-300">
                    <option value="In Progress" {{ $project->overall_status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Approved" {{ $project->overall_status === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Completed" {{ $project->overall_status === 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700">
                    Advance Stage
                </button>
            </form>
        </div>

        <!-- Checklist Sections (Directly from SOP document) -->
        @php
            $sections = $project->checklists->groupBy('section_title');
        @endphp

        <div class="space-y-6">
            @foreach ($sections as $sectionTitle => $items)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-3.5 bg-slate-900 text-white flex items-center justify-between">
                        <h3 class="text-xs font-bold tracking-wider uppercase text-cyan-300">{{ $sectionTitle }}</h3>
                        <span class="text-[11px] text-slate-400">
                            {{ $items->where('status', 'yes')->count() }} / {{ $items->count() }} Verified
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        @foreach ($items as $chk)
                            <div class="p-4 flex flex-wrap items-center justify-between gap-3 hover:bg-slate-50/70 transition-colors">
                                <div class="flex-1 min-w-[280px]">
                                    <div class="font-semibold text-slate-800">{{ $chk->checklist_item }}</div>
                                    @if ($chk->remarks)
                                        <div class="text-[11px] text-slate-500 mt-0.5 italic">Remarks: {{ $chk->remarks }}</div>
                                    @endif
                                    @if ($chk->verified_at)
                                        <div class="text-[10px] text-slate-400 mt-0.5">Signed off by {{ $chk->verifier->name ?? 'Supervisor' }} &middot; {{ $chk->verified_at->format('d M Y, h:i A') }}</div>
                                    @endif
                                </div>

                                <div>
                                    <form action="{{ route('sop.update-checklist', $chk) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="text" name="remarks" value="{{ $chk->remarks }}" placeholder="Remarks..." class="text-[11px] py-1 px-2 rounded-lg border-slate-200 w-44" />
                                        
                                        <button type="submit" name="status" value="yes" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $chk->status === 'yes' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                                            ✓ Yes
                                        </button>
                                        <button type="submit" name="status" value="no" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $chk->status === 'no' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-rose-50 hover:text-rose-700' }}">
                                            ✕ No
                                        </button>
                                        <button type="submit" name="status" value="na" class="px-2 py-1 rounded-lg text-xs font-medium {{ $chk->status === 'na' ? 'bg-slate-600 text-white' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                                            N/A
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>
