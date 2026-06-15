@php
$typeBadge = match($node->node_type) {
    'PART' => 'bg-indigo-100 text-indigo-800',
    'CHAPTER' => 'bg-purple-100 text-purple-800',
    'SECTION' => 'bg-blue-100 text-blue-800',
    'SUBSECTION' => 'bg-cyan-100 text-cyan-800',
    'PARAGRAPH' => 'bg-teal-100 text-teal-800',
    'SUBPARAGRAPH' => 'bg-green-100 text-green-800',
    'ITEM' => 'bg-lime-100 text-lime-800',
    'SCHEDULE' => 'bg-amber-100 text-amber-800',
    'PREAMBLE' => 'bg-orange-100 text-orange-800',
    default => 'bg-gray-100 text-gray-600',
};
@endphp

<details class="group" {{ $depth < 2 ? 'open' : '' }}>
    <summary class="flex items-start gap-2 cursor-pointer rounded px-3 py-2 hover:bg-gray-50 select-none
                    {{ $depth === 0 ? 'bg-gray-100 font-semibold' : '' }}"
             style="padding-left: {{ ($depth * 1.25) + 0.75 }}rem">
        <span class="mt-0.5 text-gray-400 group-open:rotate-90 transition-transform text-xs">▶</span>
        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium {{ $typeBadge }}">{{ $node->node_type }}</span>
        @if($node->node_no)
            <span class="text-sm font-medium text-gray-800">{{ $node->node_no }}</span>
        @endif
        @if($node->heading)
            <span class="text-sm text-gray-700">{{ $node->heading }}</span>
        @endif
        @if(!$node->has_children && $node->text_content)
            <span class="text-xs text-gray-400 truncate max-w-xs ml-1">{{ Str::limit($node->text_content, 80) }}</span>
        @endif
        <span class="ml-auto flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="{{ route('admin.nodes.show', $node) }}" class="text-xs text-indigo-600 hover:text-indigo-900 whitespace-nowrap">View</a>
            <a href="{{ route('admin.nodes.edit', $node) }}" class="text-xs text-yellow-600 hover:text-yellow-900 whitespace-nowrap">Edit</a>
            <a href="{{ route('admin.nodes.create', ['act_id' => $node->act_id, 'parent_node_id' => $node->node_id]) }}"
               class="text-xs text-green-600 hover:text-green-900 whitespace-nowrap">+Child</a>
            <form method="POST" action="{{ route('admin.nodes.destroy', $node) }}" class="inline"
                  onsubmit="return confirm('Delete this node and all children?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-600 hover:text-red-900">Del</button>
            </form>
        </span>
    </summary>

    @if($node->has_children)
        <div class="border-l border-gray-200 ml-4">
            @foreach($node->allChildren as $child)
                @include('admin.nodes._node', ['node' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @elseif($node->text_content)
        <div class="px-4 py-2 text-sm text-gray-600 leading-relaxed bg-white border-l border-gray-200 ml-4"
             style="padding-left: {{ (($depth + 1) * 1.25) + 0.75 }}rem">
            {{ $node->text_content }}
        </div>
    @endif
</details>
