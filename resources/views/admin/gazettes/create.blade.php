@extends('admin.layouts.app')
@section('title', 'New Gazette')
@section('heading', 'New Gazette')

@section('content')
<div class="max-w-2xl bg-white shadow rounded-lg p-6">
    <form method="POST" action="{{ route('admin.gazettes.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Gazette No</label>
            <input type="text" name="gazette_no" value="{{ old('gazette_no') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Date</label>
            <input type="date" name="gazette_date" value="{{ old('gazette_date') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Save</button>
            <a href="{{ route('admin.gazettes.index') }}" class="rounded bg-white px-4 py-2 text-sm font-medium text-gray-700 border hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
