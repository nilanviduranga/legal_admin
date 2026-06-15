@extends('admin.layouts.app')
@section('title', 'Node Viewer — ' . ($act->short_title ?? $act->title))
@section('heading', 'Node Viewer')

@section('breadcrumb')
    <a href="{{ route('admin.acts.index') }}" class="hover:text-indigo-600 transition-colors">Acts</a>
    <span class="mx-1">›</span>
    <a href="{{ route('admin.acts.show', $act) }}" class="hover:text-indigo-600 transition-colors truncate max-w-xs">
        Act {{ $act->act_no }}/{{ $act->act_year }}
    </a>
    <span class="mx-1">›</span>
    <span class="text-gray-600">Nodes</span>
@endsection

@section('action_button')
<div class="flex gap-2">
    <a href="{{ route('admin.nodes.create', ['act_id' => $act->act_id]) }}"
       class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add Node
    </a>
    <a href="{{ route('admin.acts.show', $act) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back to Act
    </a>
</div>
@endsection

@section('content')

{{-- Act summary strip --}}
<div class="mb-5 flex items-center gap-4 bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
    <div class="flex-1 min-w-0">
        <p class="text-xs text-gray-400 mb-0.5">Act {{ $act->act_no }} of {{ $act->act_year }}</p>
        <p class="text-sm font-semibold text-gray-900 truncate">{{ $act->title }}</p>
    </div>
    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold shrink-0
        {{ $act->status === 'active' ? 'bg-green-100 text-green-700' :
           ($act->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
        {{ ucfirst($act->status) }}
    </span>
    <span class="text-xs text-gray-400 shrink-0">{{ $nodes->count() }} top-level node(s)</span>
</div>

{{-- Node tree --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Legal Structure</h2>
        <div class="flex gap-2">
            <button onclick="document.querySelectorAll('details').forEach(d=>d.open=true)"
                    class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors">
                Expand all
            </button>
            <button onclick="document.querySelectorAll('details').forEach(d=>d.open=false)"
                    class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors">
                Collapse all
            </button>
        </div>
    </div>
    <div class="p-3">
        @forelse($nodes as $node)
            @include('admin.nodes._node', ['node' => $node, 'depth' => 0])
        @empty
        <div class="py-12 text-center">
            <p class="text-sm text-gray-400">This act has no nodes yet.</p>
            <p class="text-xs text-gray-300 mt-1">Import the act's PDF or add nodes manually.</p>
        </div>
        @endforelse
    </div>
</div>

@endsection
