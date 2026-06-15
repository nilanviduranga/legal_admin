@extends('admin.layouts.app')
@section('title', 'Edit Act')
@section('heading', 'Edit Act — ' . $act->title)

@section('content')
<div class="max-w-2xl bg-white shadow rounded-lg p-6">
    <form method="POST" action="{{ route('admin.acts.update', $act) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700">Gazette</label>
            <select name="gazette_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @foreach($gazettes as $gazette)
                    <option value="{{ $gazette->gazette_id }}" {{ old('gazette_id', $act->gazette_id) == $gazette->gazette_id ? 'selected' : '' }}>
                        {{ $gazette->gazette_no }} — {{ $gazette->gazette_date->format('d M Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Act No</label>
                <input type="number" name="act_no" value="{{ old('act_no', $act->act_no) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Year</label>
                <input type="number" name="act_year" value="{{ old('act_year', $act->act_year) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title', $act->title) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Short Title</label>
            <input type="text" name="short_title" value="{{ old('short_title', $act->short_title) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Commencement Date</label>
            <input type="date" name="commencement_date" value="{{ old('commencement_date', $act->commencement_date?->format('Y-m-d')) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="active" {{ old('status', $act->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="repealed" {{ old('status', $act->status) === 'repealed' ? 'selected' : '' }}>Repealed</option>
                <option value="amended" {{ old('status', $act->status) === 'amended' ? 'selected' : '' }}>Amended</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Update</button>
            <a href="{{ route('admin.acts.show', $act) }}" class="rounded bg-white px-4 py-2 text-sm font-medium text-gray-700 border hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
