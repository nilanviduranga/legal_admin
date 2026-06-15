@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('action_button')
<a href="{{ route('admin.import.form') }}"
   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
    </svg>
    Import PDF
</a>
@endsection

@section('content')

{{-- ─── Stat cards ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Gazettes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['gazettes']) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('admin.gazettes.index') }}" class="mt-3 inline-flex items-center text-xs text-blue-600 hover:text-blue-700 font-medium">
            View all <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Acts</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['acts']) }}</p>
            </div>
            <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('admin.acts.index') }}" class="mt-3 inline-flex items-center text-xs text-violet-600 hover:text-violet-700 font-medium">
            View all <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Legal Nodes</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['nodes']) }}</p>
            </div>
            <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-gray-400">Sections, parts, schedules</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cross-references</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['references']) }}</p>
            </div>
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('admin.cross-references.index') }}" class="mt-3 inline-flex items-center text-xs text-amber-600 hover:text-amber-700 font-medium">
            View all <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
    </div>

</div>

{{-- ─── Two-column body ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent acts (2/3 width) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Recent Acts</h2>
            <a href="{{ route('admin.acts.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">View all</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentActs as $act)
            <div class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-xs font-semibold text-gray-500">Act {{ $act->act_no }}/{{ $act->act_year }}</span>
                        @if($act->gazette)
                            <span class="text-xs text-gray-400">· Gazette {{ $act->gazette->gazette_no }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-800 truncate">{{ $act->title }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $act->status === 'active' ? 'bg-green-100 text-green-700' :
                           ($act->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($act->status) }}
                    </span>
                    <a href="{{ route('admin.acts.show', $act) }}"
                       class="text-xs text-gray-400 hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">No acts yet.</p>
                <a href="{{ route('admin.import.form') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-700 font-medium">Import a PDF</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Right column (1/3 width) --}}
    <div class="space-y-5">

        {{-- Acts by status --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Acts by Status</h2>
            @php
                $total = array_sum($actsByStatus);
                $statusConfig = [
                    'active'   => ['label' => 'Active',   'color' => 'bg-green-500'],
                    'amended'  => ['label' => 'Amended',  'color' => 'bg-amber-500'],
                    'repealed' => ['label' => 'Repealed', 'color' => 'bg-red-400'],
                ];
            @endphp
            @if($total > 0)
                <div class="space-y-3">
                    @foreach($statusConfig as $key => $cfg)
                    @php $count = $actsByStatus[$key] ?? 0; $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span class="font-medium">{{ $cfg['label'] }}</span>
                            <span>{{ $count }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100">
                            <div class="h-2 rounded-full {{ $cfg['color'] }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400">No acts in the database yet.</p>
            @endif
        </div>

        {{-- Node types --}}
        @if($nodesByType->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Node Types</h2>
            @php
                $typeColors = [
                    'PART'        => 'bg-indigo-100 text-indigo-700',
                    'CHAPTER'     => 'bg-purple-100 text-purple-700',
                    'SECTION'     => 'bg-blue-100 text-blue-700',
                    'SUBSECTION'  => 'bg-cyan-100 text-cyan-700',
                    'PARAGRAPH'   => 'bg-teal-100 text-teal-700',
                    'ITEM'        => 'bg-lime-100 text-lime-700',
                    'SCHEDULE'    => 'bg-amber-100 text-amber-700',
                    'PREAMBLE'    => 'bg-orange-100 text-orange-700',
                    'DEFINITIONS' => 'bg-rose-100 text-rose-700',
                ];
            @endphp
            <div class="flex flex-wrap gap-2">
                @foreach($nodesByType as $row)
                @php $color = $typeColors[$row->node_type] ?? 'bg-gray-100 text-gray-600'; @endphp
                <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium {{ $color }}">
                    {{ $row->node_type }}
                    <span class="font-bold">{{ number_format($row->count) }}</span>
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Quick links --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.gazettes.create') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-gray-50 hover:bg-indigo-50 hover:text-indigo-700 text-sm text-gray-700 font-medium transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Gazette
                </a>
                <a href="{{ route('admin.acts.create') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-gray-50 hover:bg-indigo-50 hover:text-indigo-700 text-sm text-gray-700 font-medium transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Act
                </a>
                <a href="{{ route('admin.search') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-gray-50 hover:bg-indigo-50 hover:text-indigo-700 text-sm text-gray-700 font-medium transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/></svg>
                    Search Legal Text
                </a>
            </div>
        </div>

    </div>
</div>

@endsection
