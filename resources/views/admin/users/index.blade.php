@extends('admin.layouts.app')
@section('title', 'User Management')
@section('heading', 'User Management')

@section('action_button')
<a href="{{ route('admin.users.create') }}"
   class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    New User
</a>
@endsection

@section('content')

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-5">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Name or email…"
                   class="w-full rounded-xl border-gray-200 bg-white pl-9 pr-4 py-2 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
        <select name="role" class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 pr-8">
            <option value="">All</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>User</option>
        </select>
    </div>
    <button type="submit"
            class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
        Filter
    </button>
    @if(request()->hasAny(['q','role']))
    <a href="{{ route('admin.users.index') }}"
       class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Clear
    </a>
    @endif
</form>

<div class="text-xs text-gray-400 mb-3">{{ $users->total() }} user(s)</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Chat Sessions</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold uppercase shrink-0"
                             style="background: linear-gradient(135deg, #1B6FD8, #2680F0);">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                        @if($user->id === auth()->id())
                        <span class="text-xs text-gray-400">(you)</span>
                        @endif
                    </div>
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-500">{{ $user->email }}</td>
                <td class="px-4 py-3.5">
                    @if($user->is_admin)
                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Admin</span>
                    @else
                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">User</span>
                    @endif
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-500">{{ $user->chat_sessions_count }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                              onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                    @if(request()->hasAny(['q','role']))
                        No users match the current filters.
                        <a href="{{ route('admin.users.index') }}" class="ml-1 text-blue-600">Clear filters</a>
                    @else
                        No users yet.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $users->withQueryString()->links() }}</div>

@endsection
