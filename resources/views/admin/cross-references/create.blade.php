@extends('admin.layouts.app')
@section('title', 'Add Cross-Reference')
@section('heading', 'Add Cross-Reference')

@section('breadcrumb')
    <a href="{{ route('admin.cross-references.index') }}" class="hover:text-indigo-600 transition-colors">Cross-references</a>
    <span class="mx-1">›</span>
    <span class="text-gray-600">Add</span>
@endsection

@section('action_button')
<a href="{{ route('admin.cross-references.index') }}"
   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Cancel
</a>
@endsection

@section('content')

@if($sourceNode)
<div class="mb-4 flex items-center gap-2 text-sm bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-3 text-indigo-800">
    <svg class="w-4 h-4 shrink-0 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
    </svg>
    Source pre-filled from node:
    <span class="font-semibold">{{ $sourceNode->getFullPath() }}</span>
</div>
@endif

<div class="max-w-2xl bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <form method="POST" action="{{ route('admin.cross-references.store') }}" class="space-y-6">
        @csrf
        @if($sourceNode)
            <input type="hidden" name="redirect_node_id" value="{{ $sourceNode->node_id }}">
        @endif

        @php
            $sourceActId  = $sourceNode?->act_id;
            $sourceNodeId = $sourceNode?->node_id;
        @endphp

        {{-- Source picker --}}
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Source (the referring section)</p>
            @include('admin.cross-references._node_picker', [
                'pickerPrefix'   => 'source',
                'acts'           => $acts,
                'selectedActId'  => $sourceActId ?? old('source_act_id'),
                'selectedNodeId' => $sourceNodeId ?? old('source_node_id'),
            ])
        </div>

        {{-- Target picker --}}
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Target (the section being referenced)</p>
            @include('admin.cross-references._node_picker', [
                'pickerPrefix'   => 'target',
                'acts'           => $acts,
                'selectedActId'  => old('target_act_id'),
                'selectedNodeId' => old('target_node_id'),
            ])
        </div>

        {{-- Type + text --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference Type <span class="text-red-500">*</span></label>
                <select name="reference_type" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach([
                        'refers_to'     => 'Refers to',
                        'subject_to'    => 'Subject to',
                        'as_amended_by' => 'As amended by',
                        'repeals'       => 'Repeals',
                        'saves'         => 'Saves',
                        'cross_applies' => 'Cross-applies',
                    ] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('reference_type') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference Text <span class="text-xs text-gray-400">(optional)</span></label>
                <input type="text" name="reference_text" value="{{ old('reference_text') }}"
                       placeholder='e.g. "under section 19"'
                       class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                Save Cross-Reference
            </button>
            <a href="{{ route('admin.cross-references.index') }}"
               class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
