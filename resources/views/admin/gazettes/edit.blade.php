@extends('admin.layouts.app')
@section('title', 'Edit Gazette')
@section('heading', 'Edit Gazette')

@section('content')
<div class="max-w-2xl bg-white shadow rounded-lg p-6">
    <form method="POST" action="{{ route('admin.gazettes.update', $gazette) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700">Gazette No</label>
            <input type="text" name="gazette_no" value="{{ old('gazette_no', $gazette->gazette_no) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Date</label>
            <input type="date" name="gazette_date" value="{{ old('gazette_date', $gazette->gazette_date?->format('Y-m-d')) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title', $gazette->title) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="active" {{ old('status', $gazette->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $gazette->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Update</button>
            <a href="{{ route('admin.gazettes.index') }}" class="rounded bg-white px-4 py-2 text-sm font-medium text-gray-700 border hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
