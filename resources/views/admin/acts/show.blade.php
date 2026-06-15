@extends('admin.layouts.app')
@section('title', $act->short_title ?? $act->title)
@section('heading', 'Act No. ' . $act->act_no . ' of ' . $act->act_year . ' — ' . $act->title)

@section('content')
{{-- Act header --}}
<div class="mb-6 bg-white shadow rounded-lg p-6">
    <dl class="grid grid-cols-3 gap-4 text-sm">
        <div><dt class="font-medium text-gray-500">Gazette</dt><dd class="mt-1 text-gray-900">{{ $act->gazette?->gazette_no }}</dd></div>
        <div><dt class="font-medium text-gray-500">Commencement</dt><dd class="mt-1 text-gray-900">{{ $act->commencement_date?->format('d M Y') ?? '—' }}</dd></div>
        <div><dt class="font-medium text-gray-500">Status</dt><dd class="mt-1">
            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                {{ $act->status === 'active' ? 'bg-green-100 text-green-800' :
                   ($act->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800') }}">
                {{ $act->status }}
            </span>
        </dd></div>
    </dl>
    <div class="mt-4 flex gap-3">
        <a href="{{ route('admin.acts.edit', $act) }}" class="rounded bg-yellow-100 px-3 py-1.5 text-sm font-medium text-yellow-800 hover:bg-yellow-200">Edit Act</a>
        <a href="{{ route('admin.search') }}?q={{ urlencode($act->title) }}" class="rounded bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200">Search</a>
    </div>
</div>

{{-- Node tree --}}
<h2 class="text-lg font-medium text-gray-900 mb-3">Legal Structure</h2>
<div class="space-y-1">
    @foreach($act->topLevelNodes as $node)
        @include('admin.nodes._node', ['node' => $node, 'depth' => 0])
    @endforeach
</div>
@endsection
