<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — LexAdmin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900">
<div class="min-h-full flex">

    {{-- Left branding panel --}}
    <div class="hidden lg:flex lg:w-1/2 bg-indigo-700 relative overflow-hidden flex-col justify-center p-16">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-indigo-900 opacity-80"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-2xl leading-none">LexAdmin</p>
                    <p class="text-indigo-200 text-sm mt-0.5">Consumer Affairs Authority</p>
                </div>
            </div>

            <h1 class="text-white text-4xl font-bold leading-tight mb-4">
                Law Document<br>Management System
            </h1>
            <p class="text-indigo-200 text-base leading-relaxed max-w-sm">
                Manage gazettes, acts, legal nodes, and cross-references. Import PDF legislation and search across the full legal corpus.
            </p>

            <div class="mt-12 grid grid-cols-2 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                    <svg class="w-5 h-5 text-indigo-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    <p class="text-white text-sm font-semibold">PDF Import</p>
                    <p class="text-indigo-200/70 text-xs mt-0.5">AI-powered parsing</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                    <svg class="w-5 h-5 text-indigo-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <p class="text-white text-sm font-semibold">Full-text Search</p>
                    <p class="text-indigo-200/70 text-xs mt-0.5">Across all legislation</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                    <svg class="w-5 h-5 text-indigo-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                    <p class="text-white text-sm font-semibold">Cross-references</p>
                    <p class="text-indigo-200/70 text-xs mt-0.5">Track legal linkages</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                    <svg class="w-5 h-5 text-indigo-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    <p class="text-white text-sm font-semibold">Node Viewer</p>
                    <p class="text-indigo-200/70 text-xs mt-0.5">Browse act structure</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right login panel --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">

            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-xl">LexAdmin</span>
            </div>

            <h2 class="text-2xl font-bold text-white mb-1">Welcome back</h2>
            <p class="text-slate-400 text-sm mb-8">Sign in to access the admin panel</p>

            @if($errors->any())
            <div class="mb-5 flex items-start gap-3 rounded-xl bg-red-900/40 border border-red-500/30 px-4 py-3">
                <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-red-300">{{ $errors->first() }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm px-4 py-3
                               placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                               transition-colors"
                        placeholder="admin@example.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm px-4 py-3
                               placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                               transition-colors"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-indigo-600
                                   focus:ring-indigo-500 focus:ring-offset-slate-900"
                        >
                        <span class="text-sm text-slate-400 select-none">Keep me signed in</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white
                           hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500
                           focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors"
                >
                    Sign in to LexAdmin
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-slate-600">
                Consumer Affairs Authority — Law Document Management System
            </p>
        </div>
    </div>

</div>
</body>
</html>
