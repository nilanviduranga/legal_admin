<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — Ludexora</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Animated circuit grid */
        .circuit-grid {
            background-image:
                linear-gradient(rgba(43, 127, 191, 0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(43, 127, 191, 0.07) 1px, transparent 1px);
            background-size: 48px 48px;
            animation: gridDrift 30s linear infinite;
        }
        @keyframes gridDrift {
            0%   { background-position: 0 0; }
            100% { background-position: 48px 48px; }
        }
        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(60px);
            animation: orbFloat 12s ease-in-out infinite;
        }
        .orb-1 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(27,111,216,0.18) 0%, transparent 70%);
            top: -80px; left: -80px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(77,163,255,0.12) 0%, transparent 70%);
            bottom: -60px; left: 30%;
            animation-delay: -4s;
        }
        .orb-3 {
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(56,189,248,0.1) 0%, transparent 70%);
            top: 40%; right: -60px;
            animation-delay: -8s;
        }
        @keyframes orbFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            33%       { transform: translateY(-24px) scale(1.04); }
            66%       { transform: translateY(12px) scale(0.97); }
        }
        /* Node pulse */
        .node-pulse {
            animation: nodePulse 3s ease-in-out infinite;
        }
        @keyframes nodePulse {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50%       { opacity: 1;   transform: scale(1.5); }
        }
        /* Logo breathe */
        .logo-breathe {
            animation: logoBreathe 4s ease-in-out infinite;
        }
        @keyframes logoBreathe {
            0%, 100% { filter: drop-shadow(0 0 14px rgba(77,163,255,0.5)); }
            50%       { filter: drop-shadow(0 0 28px rgba(77,163,255,0.85)); }
        }
        /* Shimmer line */
        .shimmer-line {
            background: linear-gradient(90deg, transparent 0%, rgba(77,163,255,0.6) 50%, transparent 100%);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%   { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
    </style>
</head>
<body class="h-full" style="background: #071422;">

<div class="min-h-full flex">

    {{-- ── Left branding panel ──────────────────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-[52%] relative overflow-hidden flex-col justify-between p-14"
         style="background: linear-gradient(160deg, #071422 0%, #0B1E35 55%, #0D2448 100%);">

        <!-- Animated grid -->
        <div class="circuit-grid absolute inset-0 opacity-100"></div>

        <!-- Floating orbs -->
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <!-- Decorative circuit nodes -->
        <div class="absolute top-24 right-16 w-2 h-2 rounded-full node-pulse" style="background:#4DA3FF; animation-delay:0s;"></div>
        <div class="absolute top-44 right-36 w-1.5 h-1.5 rounded-full node-pulse" style="background:#2680F0; animation-delay:0.8s;"></div>
        <div class="absolute bottom-32 left-20 w-2 h-2 rounded-full node-pulse" style="background:#4DA3FF; animation-delay:1.5s;"></div>
        <div class="absolute bottom-48 right-24 w-1.5 h-1.5 rounded-full node-pulse" style="background:#38BDF8; animation-delay:2.2s;"></div>
        <div class="absolute top-1/2 right-8 w-1 h-1 rounded-full node-pulse" style="background:#4DA3FF; animation-delay:0.4s;"></div>
        <div class="absolute top-1/3 left-32 w-1.5 h-1.5 rounded-full node-pulse" style="background:#2680F0; animation-delay:1.2s;"></div>

        <!-- Content -->
        <div class="relative z-10">
            <!-- Logo + Name -->
            <div class="flex items-center gap-4 mb-14">
                <div class="logo-breathe">
                    <img src="{{ asset('images/ludexora-logo.svg') }}" alt="Ludexora" class="w-16 h-16 object-contain"/>
                </div>
                <div>
                    <p class="font-extrabold text-3xl leading-none tracking-widest" style="color:#E2EBF8; letter-spacing:0.12em">LUDEXORA</p>
                    <div class="shimmer-line h-px mt-1.5 mb-1 w-40 rounded-full"></div>
                    <p class="text-sm" style="color:#3B8FD4">Legal Intelligence Platform</p>
                </div>
            </div>

            <!-- Headline -->
            <h1 class="font-bold leading-tight mb-5" style="font-size:2.4rem; color:#E2EBF8; line-height:1.15;">
                Law Document<br/>
                <span style="background: linear-gradient(90deg, #4DA3FF, #38BDF8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    Management System
                </span>
            </h1>
            <p class="text-base leading-relaxed max-w-xs" style="color:#5A8AB0;">
                Manage gazettes, acts, legal nodes, and cross-references. Import PDF legislation and search across the full legal corpus.
            </p>

            <!-- Feature cards -->
            <div class="mt-12 grid grid-cols-2 gap-3">
                <div class="lx-feature-card p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: rgba(27,111,216,0.2);">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="#4DA3FF" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold" style="color:#C8D8EC">PDF Import</p>
                    </div>
                    <p class="text-xs" style="color:#4A6A8A">AI-powered parsing</p>
                </div>

                <div class="lx-feature-card p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: rgba(27,111,216,0.2);">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="#4DA3FF" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold" style="color:#C8D8EC">Full-text Search</p>
                    </div>
                    <p class="text-xs" style="color:#4A6A8A">Across all legislation</p>
                </div>

                <div class="lx-feature-card p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: rgba(27,111,216,0.2);">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="#4DA3FF" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold" style="color:#C8D8EC">Cross-references</p>
                    </div>
                    <p class="text-xs" style="color:#4A6A8A">Track legal linkages</p>
                </div>

                <div class="lx-feature-card p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: rgba(27,111,216,0.2);">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="#4DA3FF" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold" style="color:#C8D8EC">Node Viewer</p>
                    </div>
                    <p class="text-xs" style="color:#4A6A8A">Browse act structure</p>
                </div>
            </div>
        </div>

        <!-- Bottom legal note -->
        <div class="relative z-10">
            <p class="text-xs" style="color:#2A5280;">Consumer Affairs Authority &mdash; Secured Administrative Access</p>
        </div>
    </div>

    {{-- ── Right login panel ────────────────────────────────────────────── --}}
    <div class="w-full lg:w-[48%] flex items-center justify-center px-6 py-12"
         style="background: linear-gradient(180deg, #071422 0%, #09192E 100%);">

        <!-- Subtle background grid for right panel -->
        <div class="absolute inset-0 opacity-30 lg:hidden circuit-grid pointer-events-none"></div>

        <div class="w-full max-w-100 relative">

            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="logo-breathe">
                    <img src="{{ asset('images/ludexora-logo.svg') }}" alt="Ludexora" class="w-12 h-12 object-contain"/>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-widest" style="color:#E2EBF8; letter-spacing:0.1em">LUDEXORA</span>
                    <p class="text-xs" style="color:#3B8FD4">Legal Intelligence Platform</p>
                </div>
            </div>

            {{-- Login card --}}
            <div class="lx-login-panel px-8 py-9">

                <div class="mb-7">
                    <h2 class="text-2xl font-bold mb-1" style="color:#E2EBF8">Welcome back</h2>
                    <p class="text-sm" style="color:#5A8AB0">Sign in to access the admin panel</p>
                </div>

                @if($errors->any())
                <div class="mb-6 flex items-start gap-3 rounded-xl px-4 py-3" style="background: rgba(220,38,38,0.12); border: 1px solid rgba(220,38,38,0.3);">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" style="color:#F87171">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm" style="color:#FCA5A5">{{ $errors->first() }}</p>
                </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold mb-2 uppercase tracking-wider" style="color:#5A8AB0;">Email address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            class="lx-input"
                            placeholder="admin@example.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold mb-2 uppercase tracking-wider" style="color:#5A8AB0;">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="lx-input"
                            placeholder="••••••••"
                        >
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 rounded"
                                style="accent-color: #1B6FD8;"
                            >
                            <span class="text-sm" style="color:#5A8AB0">Keep me signed in</span>
                        </label>
                    </div>

                    <button type="submit" class="lx-btn-primary mt-2">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                            </svg>
                            Sign in to Ludexora
                        </span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="mt-7 pt-6" style="border-top: 1px solid rgba(30,58,95,0.8);">
                    <div class="flex items-center justify-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full" style="background:#1B6FD8;"></div>
                        <p class="text-xs text-center" style="color:#2A4A6A;">
                            Secured by Ludexora &mdash; Consumer Affairs Authority
                        </p>
                        <div class="w-1.5 h-1.5 rounded-full" style="background:#1B6FD8;"></div>
                    </div>
                </div>
            </div>

            <!-- Version stamp -->
            <p class="mt-5 text-center text-xs" style="color:#1E3A5F;">v2.0 &bull; Legal Intelligence Platform</p>
        </div>
    </div>

</div>
</body>
</html>
