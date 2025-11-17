<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Campus Wave')</title>
    <!-- Tailwind Play CDN for quick styling; replace with proper build for production -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* small custom tweaks */
        .glass { background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02)); backdrop-filter: blur(6px); }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-900 via-slate-900 to-sky-900 text-slate-100 min-h-screen">
    <header class="relative overflow-hidden">
        <div class="bg-gradient-to-b from-slate-900/60 to-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="text-3xl font-extrabold text-cyan-300">Campus Wave</div>
                            <div class="hidden sm:block text-sm text-sky-200/60">Water Splash Party</div>
                        </div>
                        <div class="hidden lg:block text-sm text-sky-200/60">Splash party • Good vibes • Fast pay</div>
                    </div>

                    <nav class="hidden md:flex items-center gap-3">
                        <a href="/" class="text-slate-200 hover:text-white px-3 py-1">Home</a>
                        @auth
                            @if(optional(auth()->user())->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1 rounded-md bg-slate-800 border border-slate-700 text-sky-200 hover:bg-slate-700">Dashboard</a>
                                <a href="{{ route('admin.purchases') }}" class="px-3 py-1 rounded-md bg-emerald-400 text-slate-900 font-semibold">Purchases</a>
                                <a href="{{ route('admin.users') }}" class="px-3 py-1 rounded-md bg-slate-800 border border-slate-700 text-sky-200 hover:bg-slate-700">Users</a>
                                <a href="{{ route('admin.prices') }}" class="px-3 py-1 rounded-md bg-slate-800 border border-slate-700 text-sky-200 hover:bg-slate-700">Prices</a>
                            @endif

                            <a href="{{ route('user.account') }}" class="px-3 py-1 rounded-md text-slate-200 hover:text-white">My Tickets</a>
                        @endauth
                        @guest
                            <a href="/login" class="px-3 py-1 rounded bg-cyan-500 text-slate-900 font-semibold">Login</a>
                            <a href="/register" class="px-3 py-1 rounded border border-slate-700">Register</a>
                        @endguest
                    </nav>

                    <div class="flex items-center gap-3">
                        @auth
                            <div class="hidden md:block text-slate-300">{{ auth()->user()->name }}</div>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="ml-2 px-3 py-1 rounded bg-cyan-500 text-slate-900 font-semibold">Logout</button></form>
                        @endauth

                        {{-- Mobile menu button --}}
                        <div class="md:hidden">
                            <button @click="open = !open" x-data="{open:false}" class="p-2 rounded bg-slate-800/40">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-sky-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile menu panel (Alpine) --}}
            <div x-data="{open:false}" x-cloak class="md:hidden">
                <div x-show="open" x-transition class="px-4 pb-4">
                    <div class="space-y-2">
                        <a href="/" class="block px-3 py-2 rounded text-sky-200">Home</a>
                        @auth
                            @if(optional(auth()->user())->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded text-sky-200">Dashboard</a>
                                <a href="{{ route('admin.purchases') }}" class="block px-3 py-2 rounded text-sky-200">Purchases</a>
                                <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded text-sky-200">Users</a>
                                <a href="{{ route('admin.prices') }}" class="block px-3 py-2 rounded text-sky-200">Prices</a>
                            @endif
                            <a href="{{ route('user.account') }}" class="block px-3 py-2 rounded text-sky-200">My Tickets</a>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full text-left px-3 py-2 rounded bg-cyan-500 text-slate-900 font-semibold">Logout</button></form>
                        @else
                            <a href="/login" class="block px-3 py-2 rounded bg-cyan-500 text-slate-900 font-semibold">Login</a>
                            <a href="/register" class="block px-3 py-2 rounded border border-slate-700">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Water waves SVG animation --}}
        <div class="absolute inset-x-0 bottom-0 pointer-events-none">
            <svg viewBox="0 0 1200 200" preserveAspectRatio="none" class="w-full h-32">
                <defs>
                    <linearGradient id="g1" x1="0" x2="1">
                        <stop offset="0%" stop-color="#0891b2" stop-opacity="0.18" />
                        <stop offset="100%" stop-color="#06b6d4" stop-opacity="0.08" />
                    </linearGradient>
                </defs>
                <g>
                    <path d="M0,120 C150,200 350,40 600,120 C850,200 1050,40 1200,120 L1200,200 L0,200 Z" fill="url(#g1)">
                        <animate attributeName="d" dur="6s" repeatCount="indefinite" values="M0,120 C150,200 350,40 600,120 C850,200 1050,40 1200,120 L1200,200 L0,200 Z; M0,140 C150,60 350,200 600,140 C850,80 1050,200 1200,140 L1200,200 L0,200 Z; M0,120 C150,200 350,40 600,120 C850,200 1050,40 1200,120 L1200,200 L0,200 Z"/>
                    </path>

                    <path d="M0,140 C200,80 400,200 600,140 C800,80 1000,200 1200,140 L1200,200 L0,200 Z" fill="#053f5e" opacity="0.06">
                        <animate attributeName="d" dur="8s" repeatCount="indefinite" values="M0,140 C200,80 400,200 600,140 C800,80 1000,200 1200,140 L1200,200 L0,200 Z; M0,120 C150,200 350,40 600,120 C850,200 1050,40 1200,120 L1200,200 L0,200 Z; M0,140 C200,80 400,200 600,140 C800,80 1000,200 1200,140 L1200,200 L0,200 Z"/>
                    </path>
                </g>
            </svg>
        </div>
    </header>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-rose-600 text-white">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-emerald-600 text-slate-900 font-semibold">{{ session('success') }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="mt-12 py-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-sky-200/60 text-sm">© {{ date('Y') }} Campus Wave — Made for party freaks.</div>
    </footer>
</body>
</html>
