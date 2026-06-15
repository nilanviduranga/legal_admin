@extends('admin.layouts.app')
@section('title', 'Edit Cross-Reference')
@section('heading', 'Edit Cross-Reference')

@section('breadcrumb')
    <a href="{{ route('admin.cross-references.index') }}" class="hover:text-indigo-600 transition-colors">Cross-references</a>
    <span class="mx-1">›</span>
    <span class="text-gray-600">Edit #{{ $crossReference->reference_id }}</span>
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
<div class="max-w-2xl bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <form method="POST" action="{{ route('admin.cross-references.update', $crossReference) }}" class="space-y-6">
        @csrf @method('PUT')

        {{-- Source picker --}}
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Source (the referring section)</p>
            @include('admin.cross-references._node_picker', [
                'pickerPrefix'   => 'source',
                'acts'           => $acts,
                'selectedActId'  => $crossReference->sourceNode?->act_id,
                'selectedNodeId' => $crossReference->source_node_id,
            ])
        </div>

        {{-- Target picker --}}
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Target (the section being referenced)</p>
            @include('admin.cross-references._node_picker', [
                'pickerPrefix'   => 'target',
                'acts'           => $acts,
                'selectedActId'  => $crossReference->targetNode?->act_id,
                'selectedNodeId' => $crossReference->target_node_id,
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
                        <option value="{{ $val }}" {{ $crossReference->reference_type === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference Text <span class="text-xs text-gray-400">(optional)</span></label>
                <input type="text" name="reference_text" value="{{ old('reference_text', $crossReference->reference_text) }}"
                       placeholder='e.g. "under section 19"'
                       class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                Update
            </button>
            <a href="{{ route('admin.cross-references.index') }}"
               class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
