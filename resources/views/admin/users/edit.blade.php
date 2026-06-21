@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('heading', 'Edit User')

@section('breadcrumb')
<a href="{{ route('admin.users.index') }}" class="hover:text-gray-600">Users</a>
<span class="mx-1">/</span>
<span>{{ $user->name }}</span>
@endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold uppercase shrink-0"
                 style="background: linear-gradient(135deg, #1B6FD8, #2680F0);">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="text-xs text-gray-400">{{ $user->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors @error('name') border-red-300 @enderror">
                @error('name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors @error('email') border-red-300 @enderror">
                @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 space-y-3">
                <p class="text-xs font-medium text-gray-500">Change Password <span class="font-normal text-gray-400">(leave blank to keep current)</span></p>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">New Password</label>
                    <input type="password" name="password"
                           class="w-full rounded-xl border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('password') border-red-300 @enderror">
                    @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full rounded-xl border-gray-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_admin" value="0">
                    <input type="checkbox" name="is_admin" value="1" id="is_admin"
                           {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                           {{ $user->id === auth()->id() ? 'disabled' : '' }}
                           class="sr-only peer">
                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-blue-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                </label>
                <label for="is_admin" class="text-sm font-medium text-gray-700 cursor-pointer">Admin access</label>
                @if($user->id === auth()->id())
                <span class="text-xs text-gray-400">(cannot change your own role)</span>
                @endif
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 active:scale-95 transition-all">
                    Save Changes
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @if($user->id !== auth()->id())
    <div class="mt-4 bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-red-50">
            <h3 class="text-sm font-semibold text-red-700">Danger Zone</h3>
        </div>
        <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-700">Delete this user</p>
                <p class="text-xs text-gray-400 mt-0.5">Permanently removes the account and all associated data.</p>
            </div>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="rounded-xl bg-red-50 border border-red-200 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors">
                    Delete User
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
