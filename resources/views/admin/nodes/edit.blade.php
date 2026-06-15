@extends('admin.layouts.app')
@section('title', 'Edit Node')
@section('heading', 'Edit: ' . $node->getFullPath())

@section('content')

@if(session('success'))
<div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
    {{ session('success') }}
</div>
@endif

<div class="max-w-2xl bg-white shadow rounded-lg p-6">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <span class="text-xs text-gray-400">Version {{ $node->version_no }}</span>
        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
            {{ $node->status === 'active' ? 'bg-green-100 text-green-700' : ($node->status === 'amended' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
            {{ ucfirst($node->status) }}
        </span>
        @if(!$node->replacedByNode)
        <form method="POST" action="{{ route('admin.nodes.new-version', $node) }}" class="ml-auto inline"
              onsubmit="return confirm('Create a new version? This node will be marked as amended.')">
            @csrf
            <button type="submit"
                    class="rounded-lg bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-800 hover:bg-indigo-200">
                New Version
            </button>
        </form>
        @else
        <a href="{{ route('admin.nodes.show', $node->replacedByNode) }}"
           class="ml-auto text-xs text-amber-600 hover:text-amber-800">
            Superseded → view v{{ $node->replacedByNode->version_no }}
        </a>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.nodes.update', $node) }}" class="space-y-4">
        @csrf @method('PUT')
        <input type="hidden" name="act_id" value="{{ $node->act_id }}">
        <input type="hidden" name="level" value="{{ $node->level }}">
        <input type="hidden" name="sort_order" value="{{ $node->sort_order }}">
        <div>
            <label class="block text-sm font-medium text-gray-700">Node Type</label>
            <select name="node_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                @foreach(['PART','CHAPTER','SECTION','SUBSECTION','PARAGRAPH','SUBPARAGRAPH','ITEM','SCHEDULE','PREAMBLE','DEFINITIONS'] as $type)
                    <option value="{{ $type }}" {{ $node->node_type === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Node No</label>
                <input type="text" name="node_no" value="{{ old('node_no', $node->node_no) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="active" {{ $node->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="repealed" {{ $node->status === 'repealed' ? 'selected' : '' }}>Repealed</option>
                    <option value="amended" {{ $node->status === 'amended' ? 'selected' : '' }}>Amended</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Heading</label>
            <input type="text" name="heading" value="{{ old('heading', $node->heading) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Text Content</label>
            <textarea name="text_content" rows="8"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">{{ old('text_content', $node->text_content) }}</textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Update</button>
            <a href="{{ route('admin.nodes.show', $node) }}" class="rounded bg-white px-4 py-2 text-sm font-medium text-gray-700 border hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
