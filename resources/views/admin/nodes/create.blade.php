@extends('admin.layouts.app')
@section('title', 'Add Node')
@section('heading', 'Add Node — ' . ($act->short_title ?? $act->title))

@section('breadcrumb')
    <a href="{{ route('admin.acts.index') }}" class="hover:text-indigo-600 transition-colors">Acts</a>
    <span class="mx-1">›</span>
    <a href="{{ route('admin.acts.show', $act) }}" class="hover:text-indigo-600 transition-colors truncate max-w-xs">
        Act {{ $act->act_no }}/{{ $act->act_year }}
    </a>
    <span class="mx-1">›</span>
    <a href="{{ route('admin.acts.nodes.index', $act) }}" class="hover:text-indigo-600 transition-colors">Nodes</a>
    <span class="mx-1">›</span>
    <span class="text-gray-600">Add Node</span>
@endsection

@section('action_button')
<a href="{{ $parent ? route('admin.nodes.show', $parent) : route('admin.acts.nodes.index', $act) }}"
   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Cancel
</a>
@endsection

@section('content')

@if($parent)
<div class="mb-4 flex items-center gap-2 text-sm text-gray-500 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
    </svg>
    Adding as child of:
    <a href="{{ route('admin.nodes.show', $parent) }}" class="font-medium text-amber-700 hover:text-amber-900">
        {{ $parent->getFullPath() }}
    </a>
</div>
@endif

<div class="max-w-2xl bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    @if($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.nodes.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="act_id" value="{{ $act->act_id }}">
        <input type="hidden" name="parent_node_id" value="{{ $parent?->node_id }}">
        <input type="hidden" name="level" value="{{ $parent ? $parent->level + 1 : 1 }}">
        <input type="hidden" name="sort_order" value="{{ $nextSortOrder }}">

        {{-- Type & Number --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Node Type <span class="text-red-500">*</span></label>
                <select name="node_type" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(['PART','CHAPTER','SECTION','SUBSECTION','PARAGRAPH','SUBPARAGRAPH','ITEM','SCHEDULE','PREAMBLE','DEFINITIONS'] as $type)
                        <option value="{{ $type }}" {{ old('node_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Node No</label>
                <input type="text" name="node_no" value="{{ old('node_no') }}" placeholder="e.g. 1, 2A, I"
                       class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        {{-- Heading --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Heading</label>
            <input type="text" name="heading" value="{{ old('heading') }}" placeholder="Section heading (optional)"
                   class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Text content --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Text Content</label>
            <textarea name="text_content" rows="8" placeholder="Paste or type the legal text here…"
                      class="block w-full rounded-lg border-gray-300 shadow-sm text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500">{{ old('text_content') }}</textarea>
        </div>

        {{-- Status & Effective date --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                        class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="amended" {{ old('status') === 'amended' ? 'selected' : '' }}>Amended</option>
                    <option value="repealed" {{ old('status') === 'repealed' ? 'selected' : '' }}>Repealed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Effective Date</label>
                <input type="date" name="effective_date" value="{{ old('effective_date') }}"
                       class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                Add Node
            </button>
            <a href="{{ $parent ? route('admin.nodes.show', $parent) : route('admin.acts.nodes.index', $act) }}"
               class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
