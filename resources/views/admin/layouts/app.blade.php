<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Ludexora</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50/80">

<div class="flex h-full">

    {{-- ─── Sidebar ──────────────────────────────────────────────────────── --}}
    <aside class="lx-sidebar fixed inset-y-0 left-0 w-64 flex flex-col z-20">

        {{-- Brand --}}
        <div class="lx-sidebar-brand h-[70px] flex items-center gap-3 px-4 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0 group">
                <div class="lx-logo-glow shrink-0 w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('images/ludexora-logo.svg') }}" alt="Ludexora" class="w-10 h-10 object-contain transition-transform duration-300 group-hover:scale-105"/>
                </div>
                <div class="min-w-0">
                    <p class="text-white font-bold text-base leading-none tracking-wide" style="letter-spacing:0.04em">LUDEXORA</p>
                    <p class="text-[11px] mt-0.5 truncate" style="color:#3B8FD4">Legal Intelligence Platform</p>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-6">

            {{-- Overview --}}
            <div>
                <p class="lx-section-label">Overview</p>
                <a href="{{ route('admin.dashboard') }}"
                   class="lx-nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    Dashboard
                </a>
            </div>

            {{-- Library --}}
            <div>
                <p class="lx-section-label">Library</p>
                <div class="space-y-0.5">
                    <a href="{{ route('admin.gazettes.index') }}"
                       class="lx-nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.gazettes.*') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        Gazettes
                    </a>
                    <a href="{{ route('admin.acts.index') }}"
                       class="lx-nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.acts.*') && !request()->routeIs('admin.acts.nodes.*') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        Acts
                    </a>
                    <a href="{{ route('admin.cross-references.index') }}"
                       class="lx-nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.cross-references.*') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                        </svg>
                        Cross-references
                    </a>
                    <a href="{{ route('admin.structure') }}"
                       class="lx-nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.structure*') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                        </svg>
                        Full Structure
                    </a>
                </div>
            </div>

            {{-- Tools --}}
            <div>
                <p class="lx-section-label">Tools</p>
                <div class="space-y-0.5">
                    <a href="{{ route('admin.import.form') }}"
                       class="lx-nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.import.*') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        PDF Import
                    </a>
                    <a href="{{ route('admin.search') }}"
                       class="lx-nav-item flex items-center gap-3 px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.search') ? 'active' : '' }}">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        Full-text Search
                    </a>
                </div>
            </div>

            {{-- System --}}
            @php $settingsOpen = request()->routeIs('admin.settings') || request()->routeIs('admin.users.*'); @endphp
            <div>
                <p class="lx-section-label">System</p>
                <div>
                    {{-- Settings accordion trigger --}}
                    <button type="button" id="settings-toggle"
                            onclick="toggleSettings()"
                            class="lx-nav-item w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="flex-1 text-left">Settings</span>
                        <svg id="settings-chevron" class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 {{ $settingsOpen ? 'rotate-180' : '' }}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown sub-items --}}
                    <div id="settings-submenu"
                         class="overflow-hidden transition-all duration-200 {{ $settingsOpen ? '' : 'hidden' }}">
                        <div class="mt-0.5 ml-3 pl-4 space-y-0.5"
                             style="border-left: 1px solid rgba(59,111,154,0.35);">

                            {{-- Settings page --}}
                            <a href="{{ route('admin.settings') }}"
                               class="lx-nav-item flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                System Settings
                            </a>

                            {{-- User Management --}}
                            <a href="{{ route('admin.users.index') }}"
                               class="lx-nav-item flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                </svg>
                                User Management
                            </a>

                            {{-- Sign out --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="lx-nav-item w-full flex items-center gap-3 px-3 py-2 text-sm font-medium">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                                    </svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </nav>

        {{-- User info --}}
        <div class="shrink-0 px-3 pb-4 pt-3" style="border-top: 1px solid #1E3A5F;">
            <div class="flex items-center gap-3 px-2 py-2 rounded-xl" style="background: rgba(27,111,216,0.08);">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold uppercase shrink-0"
                     style="background: linear-gradient(135deg, #1B6FD8, #2680F0); box-shadow: 0 0 10px rgba(77,163,255,0.3)">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color:#E2EBF8">{{ auth()->user()->name }}</p>
                    <p class="text-xs truncate" style="color:#3B6A9A">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </aside>

    <script>
    function toggleSettings() {
        const menu    = document.getElementById('settings-submenu');
        const chevron = document.getElementById('settings-chevron');
        const open    = !menu.classList.contains('hidden');
        if (open) {
            menu.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        } else {
            menu.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        }
    }
    </script>

    {{-- ─── Main content ─────────────────────────────────────────────────── --}}
    <div class="ml-64 flex-1 flex flex-col min-h-screen">

        {{-- Top bar --}}
        <header class="lx-topbar h-[70px] flex items-center justify-between px-6 sticky top-0 z-10 shrink-0">
            <div>
                <h1 class="text-lg font-bold text-gray-900 leading-none">@yield('heading', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <nav class="text-xs text-gray-400 mt-1 flex items-center gap-1">@yield('breadcrumb')</nav>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @hasSection('action_button')
                    <div>@yield('action_button')</div>
                @endif
                {{-- System badge --}}
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium"
                     style="background: rgba(27,111,216,0.08); color: #1B6FD8; border: 1px solid rgba(27,111,216,0.2);">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse inline-block"></span>
                    Ludexora
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @php $hasFlash = session('success') || session('error') || $errors->any(); @endphp
        @if($hasFlash)
        <div class="px-6 pt-4 space-y-2 lx-fade-in" id="flash-area">
            @if(session('success'))
            <div class="flex items-center gap-3 rounded-2xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 shadow-sm">
                <svg class="w-4 h-4 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 shadow-sm">
                <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
            @endif
            @if($errors->any())
            <div class="rounded-2xl bg-red-50 border border-red-200 px-4 py-3 shadow-sm">
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
        <main class="flex-1 px-6 py-6 lx-fade-in">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="px-6 py-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-400">Ludexora &mdash; Legal Intelligence Platform</span>
            <span class="text-xs text-gray-300">Consumer Affairs Authority</span>
        </footer>

    </div>
</div>

{{-- Global hover tooltip --}}
<div id="lex-tip"
     style="display:none;position:fixed;z-index:9999;pointer-events:none;max-width:440px"
     class="text-sm rounded-2xl px-4 py-3 leading-relaxed whitespace-pre-wrap">
</div>
<script>
(function(){
    const tip = document.getElementById('lex-tip');
    let showTimer, activeEl;

    document.addEventListener('mouseover', function(e) {
        const el = e.target.closest('[data-tooltip]');
        if (!el || !el.dataset.tooltip.trim()) return;
        if (activeEl === el) return;
        activeEl = el;
        clearTimeout(showTimer);
        showTimer = setTimeout(function() {
            tip.textContent = el.dataset.tooltip;
            tip.style.left = '-9999px'; tip.style.top = '-9999px'; tip.style.display = 'block';
            positionTip(e);
        }, 350);
    }, false);

    document.addEventListener('mousemove', function(e) {
        if (tip.style.display === 'none') return;
        positionTip(e);
    }, false);

    document.addEventListener('mouseout', function(e) {
        if (!e.target.closest('[data-tooltip]')) return;
        activeEl = null;
        clearTimeout(showTimer);
        tip.style.display = 'none';
    }, false);

    function positionTip(e) {
        const pad = 14, tw = tip.offsetWidth, th = tip.offsetHeight;
        let x = e.clientX + pad, y = e.clientY + pad;
        if (x + tw + 8 > window.innerWidth)  x = e.clientX - tw - pad;
        if (y + th + 8 > window.innerHeight) y = e.clientY - th - pad;
        tip.style.left = Math.max(8, x) + 'px';
        tip.style.top  = Math.max(8, y) + 'px';
    }
})();
</script>
</body>
</html>
