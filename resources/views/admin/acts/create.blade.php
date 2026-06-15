@extends('admin.layouts.app')
@section('title', 'New Act')
@section('heading', 'New Act')

@section('content')
<div class="max-w-2xl bg-white shadow rounded-lg p-6">
    <form method="POST" action="{{ route('admin.acts.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Gazette</label>
            <select name="gazette_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">— Select —</option>
                @foreach($gazettes as $gazette)
                    <option value="{{ $gazette->gazette_id }}" {{ old('gazette_id') == $gazette->gazette_id ? 'selected' : '' }}>
                        {{ $gazette->gazette_no }} — {{ $gazette->gazette_date->format('d M Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Act No</label>
                <input type="number" name="act_no" value="{{ old('act_no') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Year</label>
                <input type="number" name="act_year" value="{{ old('act_year') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Short Title</label>
            <input type="text" name="short_title" value="{{ old('short_title') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Commencement Date</label>
            <input type="date" name="commencement_date" value="{{ old('commencement_date') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="repealed" {{ old('status') === 'repealed' ? 'selected' : '' }}>Repealed</option>
                <option value="amended" {{ old('status') === 'amended' ? 'selected' : '' }}>Amended</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Save</button>
            <a href="{{ route('admin.acts.index') }}" class="rounded bg-white px-4 py-2 text-sm font-medium text-gray-700 border hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
