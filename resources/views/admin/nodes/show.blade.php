@extends('admin.layouts.app')
@section('title', $node->getFullPath())
@section('heading', $node->getFullPath())

@section('content')

{{-- Version / replacement banners --}}
@if($node->replacedByNode)
<div class="mb-4 flex items-center gap-2 text-sm bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-amber-800">
    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
    </svg>
    This node has been superseded.
    <a href="{{ route('admin.nodes.show', $node->replacedByNode) }}" class="font-semibold underline hover:text-amber-900">
        View current version (v{{ $node->replacedByNode->version_no }})
    </a>
</div>
@endif

@if($node->replacesNode)
<div class="mb-4 flex items-center gap-2 text-sm bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-blue-800">
    <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
    </svg>
    This is version {{ $node->version_no }}, replacing
    <a href="{{ route('admin.nodes.show', $node->replacesNode) }}" class="font-semibold underline hover:text-blue-900">
        v{{ $node->replacesNode->version_no }}
    </a>
</div>
@endif

{{-- Node content --}}
<div class="mb-6 bg-white shadow rounded-lg p-6">
    <div class="flex gap-3 flex-wrap items-center mb-4">
        <span class="inline-flex rounded px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">{{ $node->node_type }}</span>
        @if($node->node_no) <span class="font-semibold">{{ $node->node_no }}</span> @endif
        <span class="text-xs text-gray-400">v{{ $node->version_no }}</span>
        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
            {{ $node->status === 'active' ? 'bg-green-100 text-green-800' : ($node->status === 'amended' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ $node->status }}</span>
        <div class="ml-auto flex gap-2 flex-wrap">
            <a href="{{ route('admin.nodes.edit', $node) }}"
               class="rounded-lg bg-yellow-100 px-3 py-1.5 text-xs font-medium text-yellow-800 hover:bg-yellow-200">Edit</a>
            @if(!$node->replacedByNode)
            <form method="POST" action="{{ route('admin.nodes.new-version', $node) }}" class="inline"
                  onsubmit="return confirm('Create a new version? The current node will be marked as amended.')">
                @csrf
                <button type="submit"
                        class="rounded-lg bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-800 hover:bg-indigo-200">
                    New Version
                </button>
            </form>
            @endif
            <a href="{{ route('admin.nodes.create', ['act_id' => $node->act_id, 'parent_node_id' => $node->node_id]) }}"
               class="rounded-lg bg-green-100 px-3 py-1.5 text-xs font-medium text-green-800 hover:bg-green-200">+ Child Node</a>
            <a href="{{ route('admin.acts.show', $node->act) }}" class="text-sm text-indigo-600 hover:text-indigo-900">← Back to Act</a>
        </div>
    </div>
    @if($node->heading)
        <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ $node->heading }}</h2>
    @endif
    @if($node->text_content)
        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $node->text_content }}</p>
    @endif
</div>

{{-- Cross references --}}
<div class="grid grid-cols-2 gap-6">
    {{-- Outgoing --}}
    <div class="bg-white shadow rounded-lg p-5">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-medium text-gray-900">This section refers to</h3>
            <button onclick="document.getElementById('add-ref-modal').showModal()"
                    class="rounded bg-indigo-600 px-2 py-1 text-xs font-medium text-white hover:bg-indigo-700">
                + Add Reference
            </button>
        </div>
        @forelse($node->outgoingReferences as $ref)
        <div class="mb-3 p-3 bg-gray-50 rounded text-sm">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <a href="{{ route('admin.nodes.show', $ref->targetNode) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                        {{ $ref->targetNode?->getFullPath() }}
                    </a>
                    <span class="ml-1 inline-flex rounded px-1.5 py-0.5 text-xs bg-gray-200 text-gray-700">{{ $ref->reference_type }}</span>
                    @if($ref->reference_text)
                        <p class="text-gray-500 text-xs mt-1 italic">"{{ $ref->reference_text }}"</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.cross-references.destroy', $ref) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">×</button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400">No outgoing references.</p>
        @endforelse
    </div>

    {{-- Incoming --}}
    <div class="bg-white shadow rounded-lg p-5">
        <h3 class="font-medium text-gray-900 mb-3">Sections that refer to this</h3>
        @forelse($node->incomingReferences as $ref)
        <div class="mb-3 p-3 bg-gray-50 rounded text-sm">
            <a href="{{ route('admin.nodes.show', $ref->sourceNode) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                {{ $ref->sourceNode?->getFullPath() }}
            </a>
            <span class="ml-1 inline-flex rounded px-1.5 py-0.5 text-xs bg-gray-200 text-gray-700">{{ $ref->reference_type }}</span>
            @if($ref->reference_text)
                <p class="text-gray-500 text-xs mt-1 italic">"{{ $ref->reference_text }}"</p>
            @endif
        </div>
        @empty
        <p class="text-sm text-gray-400">No incoming references.</p>
        @endforelse
    </div>
</div>

{{-- Add reference — link to dedicated create page with source pre-filled --}}
{{-- The modal button in the heading already points there; keeping a dialog for quick inline add --}}
<dialog id="add-ref-modal" class="rounded-2xl shadow-xl p-6 w-full max-w-lg backdrop:bg-black/50">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-base font-semibold text-gray-900">Add Cross-Reference</h3>
        <button type="button" onclick="document.getElementById('add-ref-modal').close()"
                class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form method="POST" action="{{ route('admin.cross-references.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="source_node_id" value="{{ $node->node_id }}">
        <input type="hidden" name="redirect_node_id" value="{{ $node->node_id }}">

        <div class="p-3 bg-indigo-50 rounded-lg text-xs text-indigo-700 font-medium">
            Source: {{ $node->getFullPath() }}
        </div>

        @php
            $acts = \App\Models\Act::orderBy('act_year')->orderBy('act_no')
                ->get(['act_id', 'act_no', 'act_year', 'short_title', 'title']);
        @endphp
        @include('admin.cross-references._node_picker', [
            'pickerPrefix'   => 'target',
            'acts'           => $acts,
            'selectedActId'  => $node->act_id,
            'selectedNodeId' => null,
        ])

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference Type</label>
                <select name="reference_type"
                        class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="refers_to">Refers to</option>
                    <option value="subject_to">Subject to</option>
                    <option value="as_amended_by">As amended by</option>
                    <option value="repeals">Repeals</option>
                    <option value="saves">Saves</option>
                    <option value="cross_applies">Cross-applies</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference Text</label>
                <input type="text" name="reference_text"
                       class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder='e.g. "under section 19"'>
            </div>
        </div>

        <div class="flex gap-3 pt-1">
            <button type="submit"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                Save
            </button>
            <button type="button" onclick="document.getElementById('add-ref-modal').close()"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </button>
        </div>
    </form>
</dialog>
@endsection
