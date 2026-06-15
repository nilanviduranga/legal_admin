@extends('admin.layouts.app')
@section('title', $gazette->gazette_no)
@section('heading', $gazette->gazette_no . ' — ' . $gazette->gazette_date->format('d M Y'))

@section('content')
<div class="mb-6 bg-white shadow rounded-lg p-6">
    <dl class="grid grid-cols-2 gap-4 text-sm">
        <div><dt class="font-medium text-gray-500">Title</dt><dd class="mt-1 text-gray-900">{{ $gazette->title }}</dd></div>
        <div><dt class="font-medium text-gray-500">Status</dt><dd class="mt-1">
            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                {{ $gazette->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                {{ $gazette->status }}
            </span>
        </dd></div>
    </dl>
</div>

<h2 class="text-lg font-medium text-gray-900 mb-3">Acts in this Gazette</h2>
<div class="overflow-hidden bg-white shadow ring-1 ring-black ring-opacity-5 rounded-lg">
    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Act No</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Year</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($gazette->acts as $act)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $act->act_no }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $act->act_year }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $act->title }}</td>
                <td class="px-6 py-4 text-sm">
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800">{{ $act->status }}</span>
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('admin.acts.show', $act) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No acts.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
