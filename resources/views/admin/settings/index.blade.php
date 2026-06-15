@extends('admin.layouts.app')
@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- ─── Database ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Database</h2>
            <p class="text-xs text-gray-400 mt-0.5">Connection status and row counts</p>
        </div>
        <div class="px-6 py-5 space-y-5">
            {{-- Connection --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Connection</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ config('database.default') }} · {{ config('database.connections.' . config('database.default') . '.host', 'sqlite') }}</p>
                </div>
                @if($dbStatus === 'connected')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold px-3 py-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Connected
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 text-red-700 text-xs font-semibold px-3 py-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    Error
                </span>
                @endif
            </div>

            {{-- Row counts --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach([
                    ['table' => 'gazettes',         'label' => 'Gazettes'],
                    ['table' => 'acts',              'label' => 'Acts'],
                    ['table' => 'legal_nodes',       'label' => 'Legal Nodes (total)'],
                    ['table' => 'soft_deleted',      'label' => 'Soft-deleted Nodes'],
                    ['table' => 'cross_references',  'label' => 'Cross-references'],
                ] as $row)
                <div class="rounded-xl bg-gray-50 px-4 py-3">
                    <p class="text-xs text-gray-500">{{ $row['label'] }}</p>
                    <p class="text-xl font-bold text-gray-900 mt-0.5">{{ number_format($tableCounts[$row['table']]) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ─── Application ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Application</h2>
        </div>
        <div class="px-6 py-5">
            <dl class="grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="font-medium text-gray-500">Laravel</dt>
                    <dd class="mt-0.5 text-gray-900">{{ app()->version() }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">PHP</dt>
                    <dd class="mt-0.5 text-gray-900">{{ PHP_VERSION }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Environment</dt>
                    <dd class="mt-0.5">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                            {{ app()->environment('production') ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ app()->environment() }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Debug Mode</dt>
                    <dd class="mt-0.5">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                            {{ config('app.debug') ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ config('app.debug') ? 'On' : 'Off' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">App URL</dt>
                    <dd class="mt-0.5 text-gray-900 text-xs break-all">{{ config('app.url') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Timezone</dt>
                    <dd class="mt-0.5 text-gray-900">{{ config('app.timezone') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- ─── External panels ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Other Panels</h2>
        </div>
        <div class="px-6 py-5 space-y-3">
            <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50">
                <div>
                    <p class="text-sm font-medium text-gray-800">Filament CMS</p>
                    <p class="text-xs text-gray-400 mt-0.5">User management, Eloquent resource explorer</p>
                </div>
                <a href="{{ url('/cms') }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2 text-xs font-semibold text-white hover:bg-violet-700 transition-colors">
                    Open
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- ─── Account ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Account</h2>
        </div>
        <div class="px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-700 flex items-center justify-center text-white font-bold text-sm uppercase">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
