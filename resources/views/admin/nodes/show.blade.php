@extends('admin.layouts.app')
@section('title', $node->getFullPath())
@section('heading', $node->getFullPath())

@section('breadcrumb')
    <a href="{{ route('admin.acts.index') }}" class="hover:text-indigo-600 transition-colors">Acts</a>
    <span class="mx-1">›</span>
    <a href="{{ route('admin.acts.show', $node->act) }}" class="hover:text-indigo-600 transition-colors truncate max-w-xs">
        Act {{ $node->act->act_no }}/{{ $node->act->act_year }}
    </a>
    <span class="mx-1">›</span>
    <a href="{{ route('admin.acts.nodes.index', $node->act) }}" class="hover:text-indigo-600 transition-colors">Nodes</a>
    @if($node->parent)
        <span class="mx-1">›</span>
        <a href="{{ route('admin.nodes.show', $node->parent) }}" class="hover:text-indigo-600 transition-colors truncate max-w-xs">
            {{ $node->parent->node_type }}{{ $node->parent->node_no ? ' '.$node->parent->node_no : '' }}
        </a>
    @endif
    <span class="mx-1">›</span>
    <span class="text-gray-600 truncate max-w-xs">{{ $node->node_type }}{{ $node->node_no ? ' '.$node->node_no : '' }}</span>
@endsection

@section('action_button')
<div class="flex gap-2">
    @if(!$node->replacedByNode)
    <form method="POST" action="{{ route('admin.nodes.new-version', $node) }}" class="inline"
          onsubmit="return confirm('Create a new version? The current node will be marked as amended.')">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 border border-indigo-200 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100 transition-colors">
            New Version
        </button>
    </form>
    @endif
    <a href="{{ route('admin.nodes.edit', $node) }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Edit Full
    </a>
    <a href="{{ $node->parent ? route('admin.nodes.show', $node->parent) : route('admin.acts.nodes.index', $node->act) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Up
    </a>
</div>
@endsection

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
    Version {{ $node->version_no }}, replacing
    <a href="{{ route('admin.nodes.show', $node->replacesNode) }}" class="font-semibold underline hover:text-blue-900">
        v{{ $node->replacesNode->version_no }}
    </a>
</div>
@endif

{{-- Node info strip --}}
@php
$nodeBadgeCls = match($node->node_type) {
    'PART'         => 'bg-indigo-100 text-indigo-800',
    'CHAPTER'      => 'bg-purple-100 text-purple-800',
    'SECTION'      => 'bg-blue-100 text-blue-800',
    'SUBSECTION'   => 'bg-cyan-100 text-cyan-800',
    'PARAGRAPH'    => 'bg-teal-100 text-teal-800',
    'SUBPARAGRAPH' => 'bg-green-100 text-green-800',
    'ITEM'         => 'bg-lime-100 text-lime-800',
    'SCHEDULE'     => 'bg-amber-100 text-amber-800',
    'PREAMBLE'     => 'bg-orange-100 text-orange-800',
    default        => 'bg-gray-100 text-gray-600',
};
@endphp
<div class="mb-5 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <div class="flex flex-wrap items-center gap-3 mb-3">
        <span class="inline-flex rounded px-2 py-0.5 text-xs font-medium {{ $nodeBadgeCls }}">{{ $node->node_type }}</span>
        @if($node->node_no)
            <span class="font-semibold text-gray-900">{{ $node->node_no }}</span>
        @endif
        <span class="text-xs text-gray-400">v{{ $node->version_no }}</span>
        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
            {{ $node->status === 'active' ? 'bg-green-100 text-green-700' :
               ($node->status === 'amended' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
            {{ $node->status }}
        </span>
    </div>
    @if($node->heading)
        <h2 class="text-base font-semibold text-gray-900 mb-2">{{ $node->heading }}</h2>
    @endif
    @if($node->text_content)
        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $node->text_content }}</p>
    @endif
</div>

@php
$defaultChildType = match($node->node_type) {
    'PART'         => 'CHAPTER',
    'CHAPTER'      => 'SECTION',
    'SECTION'      => 'SUBSECTION',
    'SUBSECTION'   => 'PARAGRAPH',
    'PARAGRAPH'    => 'SUBPARAGRAPH',
    'SUBPARAGRAPH' => 'ITEM',
    'SCHEDULE'     => 'SECTION',
    'PREAMBLE'     => 'PARAGRAPH',
    'DEFINITIONS'  => 'PARAGRAPH',
    default        => 'SECTION',
};
$defaultChildTypeCls = match($defaultChildType) {
    'CHAPTER'      => 'bg-purple-100 text-purple-800',
    'SECTION'      => 'bg-blue-100 text-blue-800',
    'SUBSECTION'   => 'bg-cyan-100 text-cyan-800',
    'PARAGRAPH'    => 'bg-teal-100 text-teal-800',
    'SUBPARAGRAPH' => 'bg-green-100 text-green-800',
    'ITEM'         => 'bg-lime-100 text-lime-800',
    default        => 'bg-gray-100 text-gray-600',
};
@endphp
{{-- ── Inline Add / Edit Form for child nodes ─────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6" id="child-form-panel">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-2 h-2 rounded-full bg-indigo-500" id="cf-dot"></div>
        <h2 class="text-sm font-semibold text-gray-700" id="cf-heading">
            Add Child Node
            <span class="ml-1 text-xs font-normal text-gray-400">
                (under {{ $node->node_type }}{{ $node->node_no ? ' '.$node->node_no : '' }})
            </span>
        </h2>
    </div>

    <div id="cf-errors" class="hidden mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 space-y-0.5"></div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Type <span class="font-normal text-gray-400">(auto)</span></label>
            <div class="flex items-center h-9 px-3 rounded-xl border border-gray-200 bg-gray-100">
                <span id="cf-type-badge"
                      class="inline-flex rounded px-2 py-0.5 text-xs font-semibold {{ $defaultChildTypeCls }}">
                    {{ $defaultChildType }}
                </span>
            </div>
            <input type="hidden" id="cf-type" value="{{ $defaultChildType }}">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="node_type"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">No. <span class="font-normal text-gray-400">(optional)</span></label>
            <input type="text" id="cf-no" placeholder="e.g. 1, 2A"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="node_no"></p>
        </div>
        <div class="col-span-2">
            <label class="block text-xs font-medium text-gray-500 mb-1">Heading <span class="font-normal text-gray-400">(optional)</span></label>
            <input type="text" id="cf-heading-input" placeholder="Section heading…"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="heading"></p>
        </div>
        <div class="col-span-2 sm:col-span-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">Text Content <span class="font-normal text-gray-400">(for leaf nodes)</span></label>
            <textarea id="cf-text" rows="4" placeholder="Paste or type the legal text…"
                      class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors resize-y font-mono"></textarea>
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="text_content"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Sort Order</label>
            <input type="number" id="cf-sort" value="{{ $nextChildSortOrder }}" min="0"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="sort_order"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select id="cf-status"
                    class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                <option value="active">Active</option>
                <option value="amended">Amended</option>
                <option value="repealed">Repealed</option>
            </select>
        </div>
        <div class="col-span-2 flex items-end gap-3 pt-1">
            <button type="button" id="cf-submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 active:scale-95 transition-all">
                Add
            </button>
            <button type="button" id="cf-cancel"
                    class="hidden rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- ── Direct children list ────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3.5 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">
            Direct Children
            <span class="ml-1.5 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600" id="child-count">{{ $node->children->count() }}</span>
        </h2>
        <p class="text-xs text-gray-400 mt-0.5">Click <strong>Edit</strong> to update a child inline above · Click <strong>View →</strong> to drill into its children</p>
    </div>
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-36">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">No.</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Heading / Content</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Order</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Status</th>
                <th class="px-4 py-3 w-44"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="children-tbody">
            @forelse($node->children as $child)
            @php
            $childBadge = match($child->node_type) {
                'PART'         => 'bg-indigo-100 text-indigo-800',
                'CHAPTER'      => 'bg-purple-100 text-purple-800',
                'SECTION'      => 'bg-blue-100 text-blue-800',
                'SUBSECTION'   => 'bg-cyan-100 text-cyan-800',
                'PARAGRAPH'    => 'bg-teal-100 text-teal-800',
                'SUBPARAGRAPH' => 'bg-green-100 text-green-800',
                'ITEM'         => 'bg-lime-100 text-lime-800',
                'SCHEDULE'     => 'bg-amber-100 text-amber-800',
                'PREAMBLE'     => 'bg-orange-100 text-orange-800',
                default        => 'bg-gray-100 text-gray-600',
            };
            $childPreview = $child->heading ?? Str::limit($child->text_content ?? '', 60);
            $childTooltip = $child->heading
                ? ($child->text_content ? $child->heading . "\n\n" . $child->text_content : $child->heading)
                : ($child->text_content ?? '');
            @endphp
            <tr id="child-row-{{ $child->node_id }}"
                class="hover:bg-gray-50 transition-colors"
                data-node="{{ json_encode([
                    'node_id'      => $child->node_id,
                    'node_type'    => $child->node_type,
                    'node_no'      => $child->node_no,
                    'heading'      => $child->heading,
                    'text_content' => $child->text_content,
                    'sort_order'   => $child->sort_order,
                    'level'        => $child->level,
                    'status'       => $child->status,
                ]) }}">
                <td class="whitespace-nowrap px-5 py-3.5">
                    <span class="inline-flex rounded px-2 py-0.5 text-xs font-medium {{ $childBadge }}">{{ $child->node_type }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-sm font-medium text-gray-700">{{ $child->node_no ?? '—' }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-700 max-w-xs">
                    <span class="line-clamp-2 cursor-help" data-tooltip="{{ $childTooltip }}">{{ $childPreview ?: '—' }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-sm text-gray-400">{{ $child->sort_order }}</td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $child->status === 'active' ? 'bg-green-100 text-green-700' :
                           ($child->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($child->status) }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.nodes.show', $child) }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View →</a>
                        <button type="button" onclick="childEdit(this.closest('tr'))"
                                class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
                        <form method="POST" action="{{ route('admin.nodes.destroy', $child) }}" class="inline"
                              onsubmit="return confirm('Delete this node and all its children? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="children-empty">
                <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                    No child nodes yet. Use the form above to add the first one.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── Cross-references ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Outgoing --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-semibold text-gray-900">This node refers to</h3>
            <button onclick="document.getElementById('add-ref-modal').showModal()"
                    class="rounded-lg bg-indigo-600 px-2 py-1 text-xs font-medium text-white hover:bg-indigo-700">
                + Add Reference
            </button>
        </div>
        @forelse($node->outgoingReferences as $ref)
        <div class="mb-3 p-3 bg-gray-50 rounded-xl text-sm">
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
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Nodes that refer to this</h3>
        @forelse($node->incomingReferences as $ref)
        <div class="mb-3 p-3 bg-gray-50 rounded-xl text-sm">
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

{{-- Add reference modal --}}
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

<script>
(function () {
    const csrf         = document.querySelector('meta[name="csrf-token"]').content;
    const ACT_ID          = {{ $node->act_id }};
    const PARENT_ID       = {{ $node->node_id }};
    const CHILD_LEVEL     = {{ $node->level + 1 }};
    const BASE_SORT       = {{ $nextChildSortOrder }};
    const DEFAULT_TYPE    = '{{ $defaultChildType }}';
    let editId         = null;
    let nextSort       = BASE_SORT;

    const fType      = document.getElementById('cf-type');
    const fTypeBadge = document.getElementById('cf-type-badge');
    const fNo     = document.getElementById('cf-no');
    const fHdg    = document.getElementById('cf-heading-input');
    const fText   = document.getElementById('cf-text');
    const fSort   = document.getElementById('cf-sort');
    const fStatus = document.getElementById('cf-status');
    const submitBtn = document.getElementById('cf-submit');
    const cancelBtn = document.getElementById('cf-cancel');
    const formHdg = document.getElementById('cf-heading');
    const dot     = document.getElementById('cf-dot');
    const errBox  = document.getElementById('cf-errors');

    const TYPE_CLS = {
        PART:'bg-indigo-100 text-indigo-800', CHAPTER:'bg-purple-100 text-purple-800',
        SECTION:'bg-blue-100 text-blue-800', SUBSECTION:'bg-cyan-100 text-cyan-800',
        PARAGRAPH:'bg-teal-100 text-teal-800', SUBPARAGRAPH:'bg-green-100 text-green-800',
        ITEM:'bg-lime-100 text-lime-800', SCHEDULE:'bg-amber-100 text-amber-800',
        PREAMBLE:'bg-orange-100 text-orange-800', DEFINITIONS:'bg-gray-100 text-gray-600',
    };

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function typeBadge(type) {
        const cls = TYPE_CLS[type] ?? 'bg-gray-100 text-gray-600';
        return `<span class="inline-flex rounded px-2 py-0.5 text-xs font-medium ${cls}">${esc(type)}</span>`;
    }

    function statusBadge(s) {
        const map = {active:'bg-green-100 text-green-700', repealed:'bg-red-100 text-red-700', amended:'bg-amber-100 text-amber-700'};
        return `<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${map[s]??'bg-gray-100 text-gray-500'}">${esc(s.charAt(0).toUpperCase()+s.slice(1))}</span>`;
    }

    function clearErrors() {
        errBox.classList.add('hidden'); errBox.innerHTML = '';
        document.querySelectorAll('#child-form-panel .field-error').forEach(el => { el.textContent=''; el.classList.add('hidden'); });
    }

    function showErrors(data) {
        const fe = data.errors ?? {};
        const all = Object.values(fe).flat();
        if (all.length) { errBox.innerHTML = all.map(m=>`<p>${esc(m)}</p>`).join(''); errBox.classList.remove('hidden'); }
        document.querySelectorAll('#child-form-panel .field-error').forEach(el => {
            const msgs = fe[el.dataset.field];
            if (msgs?.length) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
        });
    }

    function setTypeBadge(type) {
        fType.value = type;
        fTypeBadge.textContent = type;
        fTypeBadge.className = 'inline-flex rounded px-2 py-0.5 text-xs font-semibold ' + (TYPE_CLS[type] ?? 'bg-gray-100 text-gray-600');
    }

    function resetForm() {
        setTypeBadge(DEFAULT_TYPE); fNo.value=''; fHdg.value=''; fText.value='';
        fSort.value = nextSort; fStatus.value = 'active';
        editId = null;
        formHdg.innerHTML = `Add Child Node <span class="ml-1 text-xs font-normal text-gray-400">(under {{ $node->node_type }}{{ $node->node_no ? ' '.$node->node_no : '' }})</span>`;
        dot.className = 'w-2 h-2 rounded-full bg-indigo-500';
        submitBtn.textContent = 'Add';
        cancelBtn.classList.add('hidden');
        clearErrors();
    }

    function buildRow(n) {
        const preview  = n.heading ?? (n.text_content ? n.text_content.substring(0,60)+(n.text_content.length>60?'…':'') : '—');
        const tooltip  = (n.heading && n.text_content) ? n.heading + '\n\n' + n.text_content : (n.heading ?? n.text_content ?? '');
        const nodeJson = esc(JSON.stringify({node_id:n.node_id,node_type:n.node_type,node_no:n.node_no,heading:n.heading,text_content:n.text_content,sort_order:n.sort_order,level:n.level,status:n.status}));
        const confirmTxt = esc('Delete this node and all its children? This cannot be undone.');
        return `
<tr id="child-row-${n.node_id}" class="hover:bg-gray-50 transition-colors" data-node="${nodeJson}">
  <td class="whitespace-nowrap px-5 py-3.5">${typeBadge(n.node_type)}</td>
  <td class="whitespace-nowrap px-4 py-3.5 text-sm font-medium text-gray-700">${esc(n.node_no??'—')}</td>
  <td class="px-4 py-3.5 text-sm text-gray-700 max-w-xs"><span class="line-clamp-2 cursor-help" data-tooltip="${esc(tooltip)}">${esc(preview)}</span></td>
  <td class="whitespace-nowrap px-4 py-3.5 text-sm text-gray-400">${esc(n.sort_order)}</td>
  <td class="whitespace-nowrap px-4 py-3.5">${statusBadge(n.status)}</td>
  <td class="whitespace-nowrap px-4 py-3.5 text-right">
    <div class="flex items-center justify-end gap-3">
      <a href="/admin/nodes/${n.node_id}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View →</a>
      <button type="button" onclick="childEdit(this.closest('tr'))" class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
      <form method="POST" action="/admin/nodes/${n.node_id}" class="inline" onsubmit="return confirm('${confirmTxt}')">
        <input type="hidden" name="_token" value="${esc(csrf)}">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
      </form>
    </div>
  </td>
</tr>`;
    }

    submitBtn.addEventListener('click', async function () {
        clearErrors(); submitBtn.disabled = true;

        const payload = {
            act_id:         ACT_ID,
            parent_node_id: editId ? PARENT_ID : PARENT_ID,
            node_type:      fType.value,
            node_no:        fNo.value.trim() || null,
            heading:        fHdg.value.trim() || null,
            text_content:   fText.value.trim() || null,
            sort_order:     parseInt(fSort.value, 10) || 0,
            level:          CHILD_LEVEL,
            status:         fStatus.value,
        };

        const url    = editId ? `/admin/nodes/${editId}` : '/admin/nodes';
        const method = editId ? 'PUT' : 'POST';

        try {
            const resp = await fetch(url, {
                method,
                headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
                body: JSON.stringify(payload),
            });

            if (resp.ok) {
                const node  = await resp.json();
                const tbody = document.getElementById('children-tbody');
                const empty = document.getElementById('children-empty');

                if (editId) {
                    const row = document.getElementById(`child-row-${editId}`);
                    if (row) row.outerHTML = buildRow(node);
                } else {
                    if (empty) empty.remove();
                    tbody.insertAdjacentHTML('beforeend', buildRow(node));
                    nextSort = payload.sort_order + 1;
                    const countEl = document.getElementById('child-count');
                    if (countEl) countEl.textContent = (parseInt(countEl.textContent)||0)+1;
                }
                resetForm();
            } else {
                showErrors(await resp.json());
            }
        } catch {
            errBox.innerHTML = '<p>Network error. Please try again.</p>';
            errBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    });

    cancelBtn.addEventListener('click', resetForm);

    window.childEdit = function (row) {
        const n = JSON.parse(row.dataset.node);
        editId = n.node_id;
        setTypeBadge(n.node_type);
        fNo.value     = n.node_no ?? '';
        fHdg.value    = n.heading ?? '';
        fText.value   = n.text_content ?? '';
        fSort.value   = n.sort_order;
        fStatus.value = n.status;

        formHdg.textContent = 'Edit Child Node';
        dot.className = 'w-2 h-2 rounded-full bg-amber-400';
        submitBtn.textContent = 'Update';
        cancelBtn.classList.remove('hidden');
        clearErrors();
        document.getElementById('child-form-panel').scrollIntoView({behavior:'smooth',block:'start'});
    };
})();
</script>

@endsection
