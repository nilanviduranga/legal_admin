@extends('admin.layouts.app')
@section('title', 'Acts')
@section('heading', 'Acts')

@section('action_button')
<a href="{{ route('admin.acts.create') }}"
   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    New Act
</a>
@endsection

@section('content')

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-5">
    <div class="flex-1 min-w-56">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Title or act number…"
                   class="w-full rounded-xl border-gray-200 bg-white pl-9 pr-4 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
        <select name="year"
                class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All years</option>
            @foreach($years as $y)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status"
                class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="repealed" {{ request('status') === 'repealed' ? 'selected' : '' }}>Repealed</option>
            <option value="amended"  {{ request('status') === 'amended'  ? 'selected' : '' }}>Amended</option>
        </select>
    </div>
    <button type="submit"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
        Filter
    </button>
    @if(request()->hasAny(['q','year','status']))
    <a href="{{ route('admin.acts.index') }}"
       class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Clear
    </a>
    @endif
</form>

<div class="text-xs text-gray-400 mb-3">{{ $acts->total() }} act(s)</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Act / Year</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Gazette</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Commencement</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nodes</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($acts as $act)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="whitespace-nowrap px-5 py-3.5">
                    <span class="text-sm font-bold text-gray-900">{{ $act->act_no }}</span>
                    <span class="text-xs text-gray-400 ml-1">/ {{ $act->act_year }}</span>
                </td>
                <td class="px-5 py-3.5 max-w-xs">
                    <p class="text-sm text-gray-800 line-clamp-2">{{ $act->title }}</p>
                    @if($act->replaced_by_act_id)
                    <span class="inline-flex items-center gap-1 text-xs text-red-500 mt-0.5">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        Replaced
                    </span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-xs text-gray-500">
                    @if($act->gazette)
                    <a href="{{ route('admin.gazettes.show', $act->gazette) }}"
                       class="text-indigo-600 hover:text-indigo-800 font-medium">
                        {{ $act->gazette->gazette_no }}
                    </a>
                    @else
                    <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-xs text-gray-500">
                    {{ $act->commencement_date?->format('d M Y') ?? '—' }}
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $act->status === 'active' ? 'bg-green-100 text-green-700' :
                           ($act->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($act->status) }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                        {{ $act->legal_nodes_count }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.acts.show', $act) }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View</a>
                        <a href="{{ route('admin.acts.nodes.index', $act) }}"
                           class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">Nodes</a>
                        <a href="{{ route('admin.acts.edit', $act) }}"
                           class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</a>
                        <form method="POST" action="{{ route('admin.acts.destroy', $act) }}" class="inline"
                              onsubmit="return confirm('Delete Act {{ $act->act_no }}/{{ $act->act_year }} and all its nodes? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                    @if(request()->hasAny(['q','year','status']))
                        No acts match the current filters.
                        <a href="{{ route('admin.acts.index') }}" class="ml-1 text-indigo-600">Clear filters</a>
                    @else
                        No acts found.
                        <a href="{{ route('admin.import.form') }}" class="ml-1 text-indigo-600">Import a PDF</a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $acts->withQueryString()->links() }}</div>

@endsection
