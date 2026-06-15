@extends('admin.layouts.app')
@section('title', 'Search')
@section('heading', 'Search Legal Text')

@section('content')
<form method="GET" class="mb-6 flex gap-3">
    <input type="text" name="q" value="{{ $query }}" placeholder="Search sections, headings, text…"
           autofocus
           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Search</button>
</form>

@if(strlen($query) > 0 && strlen($query) < 3)
    <p class="text-sm text-yellow-600">Enter at least 3 characters to search.</p>
@elseif(strlen($query) >= 3)
    <p class="text-sm text-gray-500 mb-4">
        {{ $results->count() }} result(s) for "<strong>{{ $query }}</strong>"
        @if($results->count() >= 50) (showing first 50) @endif
    </p>

    <div class="space-y-4">
        @forelse($results as $node)
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <a href="{{ route('admin.acts.show', $node->act) }}" class="text-xs text-gray-500 hover:text-indigo-600">
                            Act No. {{ $node->act->act_no }} of {{ $node->act->act_year }}
                        </a>
                        <span class="text-gray-300">›</span>
                        <span class="text-xs text-gray-500">{{ $node->getFullPath() }}</span>
                    </div>
                    @if($node->heading)
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $node->heading }}</h3>
                    @endif
                    <p class="text-sm text-gray-700 leading-relaxed">
                        {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-100 rounded px-0.5">$1</mark>', e($node->snippet)) !!}
                    </p>
                </div>
                <a href="{{ route('admin.nodes.show', $node) }}"
                   class="shrink-0 rounded bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                    View
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400 text-sm">No results found for "{{ $query }}".</div>
        @endforelse
    </div>
@endif
@endsection
