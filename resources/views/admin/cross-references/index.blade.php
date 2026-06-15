@extends('admin.layouts.app')
@section('title', 'Cross-references')
@section('heading', 'Cross-references')

@section('action_button')
<a href="{{ route('admin.cross-references.create') }}"
   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    Add Cross-Reference
</a>
@endsection

@section('content')

{{-- ─── Stat chips ──────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap gap-2 mb-6">
    @php
        $typeConfig = [
            'refers_to'     => ['label' => 'Refers to',     'bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
            'repeals'       => ['label' => 'Repeals',       'bg' => 'bg-red-100',    'text' => 'text-red-700'],
            'saves'         => ['label' => 'Saves',         'bg' => 'bg-green-100',  'text' => 'text-green-700'],
            'subject_to'    => ['label' => 'Subject to',    'bg' => 'bg-amber-100',  'text' => 'text-amber-700'],
            'cross_applies' => ['label' => 'Cross-applies', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
            'as_amended_by' => ['label' => 'As amended by', 'bg' => 'bg-teal-100',   'text' => 'text-teal-700'],
        ];
    @endphp
    <span class="inline-flex items-center gap-1.5 rounded-xl bg-gray-900 text-white px-3 py-1.5 text-xs font-semibold">
        Total <span>{{ number_format($totalCount) }}</span>
    </span>
    @foreach($typeConfig as $key => $cfg)
    @if(($typeCounts[$key] ?? 0) > 0)
    <a href="{{ route('admin.cross-references.index', ['type' => $key] + request()->except('type', 'page')) }}"
       class="inline-flex items-center gap-1.5 rounded-xl {{ $cfg['bg'] }} {{ $cfg['text'] }} px-3 py-1.5 text-xs font-semibold hover:opacity-80 transition-opacity
              {{ request('type') === $key ? 'ring-2 ring-offset-1 ring-current' : '' }}">
        {{ $cfg['label'] }} <span>{{ number_format($typeCounts[$key]) }}</span>
    </a>
    @endif
    @endforeach
</div>

{{-- ─── Filters ─────────────────────────────────────────────────────────── --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-5">
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Act</label>
        <select name="act_id"
                class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All Acts</option>
            @foreach($acts as $act)
                <option value="{{ $act->act_id }}" {{ request('act_id') == $act->act_id ? 'selected' : '' }}>
                    Act {{ $act->act_no }}/{{ $act->act_year }} — {{ Str::limit($act->title, 50) }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Reference Type</label>
        <select name="type"
                class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All Types</option>
            @foreach($typeConfig as $key => $cfg)
                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
        Filter
    </button>
    @if(request()->hasAny(['act_id','type']))
    <a href="{{ route('admin.cross-references.index') }}"
       class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Clear
    </a>
    @endif
</form>

{{-- ─── Table ───────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Source Node</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Target Node</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Reference Text</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($references as $ref)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3.5 text-sm">
                    @if($ref->sourceNode)
                    <div>
                        <a href="{{ route('admin.nodes.show', $ref->sourceNode) }}"
                           class="font-medium text-indigo-600 hover:text-indigo-800 text-xs">
                            {{ $ref->sourceNode->getFullPath() }}
                        </a>
                        @if($ref->sourceNode->act)
                        <p class="text-xs text-gray-400 mt-0.5">
                            Act {{ $ref->sourceNode->act->act_no }}/{{ $ref->sourceNode->act->act_year }}
                        </p>
                        @endif
                    </div>
                    @else
                    <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="px-4 py-3.5">
                    @php $cfg = $typeConfig[$ref->reference_type] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => $ref->reference_type]; @endphp
                    <span class="inline-flex rounded-lg {{ $cfg['bg'] }} {{ $cfg['text'] }} px-2.5 py-1 text-xs font-semibold whitespace-nowrap">
                        {{ $cfg['label'] }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-sm">
                    @if($ref->targetNode)
                    <div>
                        <a href="{{ route('admin.nodes.show', $ref->targetNode) }}"
                           class="font-medium text-indigo-600 hover:text-indigo-800 text-xs">
                            {{ $ref->targetNode->getFullPath() }}
                        </a>
                        @if($ref->targetNode->act)
                        <p class="text-xs text-gray-400 mt-0.5">
                            Act {{ $ref->targetNode->act->act_no }}/{{ $ref->targetNode->act->act_year }}
                        </p>
                        @endif
                    </div>
                    @else
                    <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 max-w-xs">
                    @if($ref->reference_text)
                    <span class="text-xs text-gray-600 italic line-clamp-2">"{{ $ref->reference_text }}"</span>
                    @else
                    <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
                <td class="px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.cross-references.edit', $ref) }}"
                           class="text-xs text-indigo-500 hover:text-indigo-700 font-medium transition-colors">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.cross-references.destroy', $ref) }}"
                              onsubmit="return confirm('Delete this cross-reference?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-400 hover:text-red-600 transition-colors font-medium">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">
                    @if(request()->hasAny(['act_id','type']))
                        No cross-references match the current filters.
                        <a href="{{ route('admin.cross-references.index') }}" class="ml-1 text-indigo-600 hover:text-indigo-700">Clear filters</a>
                    @else
                        No cross-references yet.
                        <a href="{{ route('admin.cross-references.create') }}" class="text-indigo-600 hover:text-indigo-700">Add one now</a>.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $references->links() }}</div>

@endsection
