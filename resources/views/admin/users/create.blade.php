@extends('admin.layouts.app')
@section('title', 'New User')
@section('heading', 'New User')

@section('breadcrumb')
<a href="{{ route('admin.users.index') }}" class="hover:text-gray-600">Users</a>
<span class="mx-1">/</span>
<span>New</span>
@endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Create User</h2>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors @error('name') border-red-300 @enderror">
                @error('name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors @error('email') border-red-300 @enderror">
                @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors @error('password') border-red-300 @enderror">
                @error('password')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors">
            </div>

            <div class="flex items-center gap-3 pt-1">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_admin" value="0">
                    <input type="checkbox" name="is_admin" value="1" id="is_admin"
                           {{ old('is_admin') ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-blue-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                </label>
                <label for="is_admin" class="text-sm font-medium text-gray-700 cursor-pointer">Admin access</label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 active:scale-95 transition-all">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
