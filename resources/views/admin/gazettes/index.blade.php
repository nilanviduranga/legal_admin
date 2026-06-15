@extends('admin.layouts.app')
@section('title', 'Gazettes')
@section('heading', 'Gazettes')

@section('action_button')
<a href="{{ route('admin.gazettes.create') }}"
   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    New Gazette
</a>
@endsection

@section('content')

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-5">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Gazette no. or title…"
                   class="w-full rounded-xl border-gray-200 bg-white pl-9 pr-4 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status"
                class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <button type="submit"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
        Filter
    </button>
    @if(request()->hasAny(['q','status']))
    <a href="{{ route('admin.gazettes.index') }}"
       class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Clear
    </a>
    @endif
</form>

<div class="text-xs text-gray-400 mb-3">{{ $gazettes->total() }} gazette(s)</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Gazette No</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Acts</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($gazettes as $gazette)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-gray-900">{{ $gazette->gazette_no }}</td>
                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-500">{{ $gazette->gazette_date->format('d M Y') }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-700 max-w-xs">
                    <span class="line-clamp-2">{{ $gazette->title }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                        {{ $gazette->acts_count }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $gazette->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($gazette->status) }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.gazettes.show', $gazette) }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View</a>
                        <a href="{{ route('admin.gazettes.edit', $gazette) }}"
                           class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</a>
                        <form method="POST" action="{{ route('admin.gazettes.destroy', $gazette) }}" class="inline"
                              onsubmit="return confirm('Delete gazette {{ $gazette->gazette_no }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                    @if(request()->hasAny(['q','status']))
                        No gazettes match the current filters.
                        <a href="{{ route('admin.gazettes.index') }}" class="ml-1 text-indigo-600">Clear filters</a>
                    @else
                        No gazettes yet.
                        <a href="{{ route('admin.gazettes.create') }}" class="ml-1 text-indigo-600">Add the first one</a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $gazettes->withQueryString()->links() }}</div>

@endsection
