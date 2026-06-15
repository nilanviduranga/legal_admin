<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — LexAdmin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50">

<div class="flex h-full">

    {{-- ─── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 w-64 bg-slate-900 flex flex-col z-20">

        {{-- Brand --}}
        <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-800 shrink-0">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-white font-bold text-sm leading-none truncate">LexAdmin</p>
                <p class="text-slate-500 text-xs mt-0.5 truncate">Consumer Affairs Authority</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-7">

            {{-- Overview --}}
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1.5">Overview</p>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    Dashboard
                </a>
            </div>

            {{-- Library --}}
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1.5">Library</p>
                <div class="space-y-0.5">
                    <a href="{{ route('admin.gazettes.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.gazettes.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        Gazettes
                    </a>
                    <a href="{{ route('admin.acts.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.acts.*') && !request()->routeIs('admin.acts.nodes.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        Acts
                    </a>
                    <a href="{{ route('admin.cross-references.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.cross-references.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                        </svg>
                        Cross-references
                    </a>
                </div>
            </div>

            {{-- Tools --}}
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1.5">Tools</p>
                <div class="space-y-0.5">
                    <a href="{{ route('admin.import.form') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.import.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        PDF Import
                    </a>
                    <a href="{{ route('admin.search') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.search') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        Full-text Search
                    </a>
                </div>
            </div>

            {{-- System --}}
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-1.5">System</p>
                <a href="{{ route('admin.settings') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('admin.settings') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            </div>

        </nav>

        {{-- User / Logout --}}
        <div class="border-t border-slate-800 p-4 shrink-0">
            <div class="flex items-center gap-3 mb-2.5">
                <div class="w-8 h-8 rounded-full bg-indigo-700 flex items-center justify-center text-white text-xs font-bold uppercase shrink-0">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-500
                               hover:bg-slate-800 hover:text-slate-200 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- ─── Main content ────────────────────────────────────────────────── --}}
    <div class="ml-64 flex-1 flex flex-col min-h-screen">

        {{-- Top bar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10 shrink-0">
            <div>
                <h1 class="text-lg font-semibold text-gray-900 leading-none">@yield('heading', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <nav class="text-xs text-gray-400 mt-1 flex items-center gap-1">@yield('breadcrumb')</nav>
                @endif
            </div>
            @hasSection('action_button')
                <div>@yield('action_button')</div>
            @endif
        </header>

        {{-- Flash messages --}}
        @php $hasFlash = session('success') || session('error') || $errors->any(); @endphp
        @if($hasFlash)
        <div class="px-6 pt-4 space-y-2" id="flash-area">
            @if(session('success'))
            <div class="flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                <svg class="w-4 h-4 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
            @endif
            @if($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3">
                <ul class="text-sm text-red-800 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 shrink-0 text-red-400">•</span>{{ $err }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <script>
            setTimeout(function () {
                var el = document.getElementById('flash-area');
                if (el) {
                    el.style.transition = 'opacity 0.4s';
                    el.style.opacity = '0';
                    setTimeout(function () { el.remove(); }, 400);
                }
            }, 4000);
        </script>
        @endif

        {{-- Page content --}}
        <main class="flex-1 px-6 py-6">
            @yield('content')
        </main>

    </div>
</div>

</body>
</html>
