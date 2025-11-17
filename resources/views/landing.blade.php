@extends('layouts.app')

@section('title', 'Campus Wave — Water Splash Party')

@section('content')

<!-- Hero: full width water splash look -->
<section class="relative overflow-hidden rounded-2xl" aria-label="hero">
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-cyan-900 via-sky-900 to-slate-900"></div>

    <!-- Animated wave SVGs -->
    <div class="absolute inset-x-0 bottom-0 -mb-40 opacity-60 pointer-events-none">
        <svg viewBox="0 0 1440 320" class="w-full h-80" preserveAspectRatio="none">
            <defs>
                <linearGradient id="g1" x1="0" x2="1">
                    <stop offset="0%" stop-color="#06b6d4" stop-opacity="0.22" />
                    <stop offset="100%" stop-color="#0ea5a3" stop-opacity="0.06" />
                </linearGradient>
            </defs>
            <path fill="url(#g1)") fill-opacity="1" d="M0,64L48,96C96,128,192,192,288,202.7C384,213,480,171,576,154.7C672,139,768,149,864,144C960,139,1056,117,1152,112C1248,107,1344,117,1392,122.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div class="text-left">
                <h1 class="text-5xl font-extrabold text-cyan-100 leading-tight drop-shadow-lg">Campus Wave — Water Splash Party</h1>
                <p class="mt-4 text-sky-100/80 max-w-xl">Dive into the most immersive campus water party. Realistic splash visuals, fast payments, VIP treatment. Bring your friends and make waves.</p>

                <div class="mt-8 flex gap-4">
                    <a href="#tickets" class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-cyan-400 to-sky-500 text-slate-900 font-semibold rounded-lg shadow-lg">Grab Tickets</a>
                    <a href="#about" class="inline-flex items-center gap-2 px-4 py-3 border border-slate-700 rounded-lg text-sm">About</a>
                </div>

                <div class="mt-8 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-white/5 flex items-center justify-center text-cyan-300 font-bold text-lg">CW</div>
                    <div>
                        <div class="text-sm text-sky-200">Party freaks welcome</div>
                        <div class="text-xs text-sky-300/70">Early bird, Regular, VIP — prices adjustable by admin</div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <!-- Photo + water overlay -->
                <div class="rounded-xl overflow-hidden shadow-2xl ring-1 ring-slate-800 glass">
                    <div class="relative h-96 bg-[url('https://images.unsplash.com/photo-1503264116251-35a269479413?q=80&w=1200&auto=format&fit=crop&s=9d3c4f2a3738e1b1b5f3b9b3f0a3fbd0')] bg-cover bg-center">
                        <!-- translucent water ripple using radial gradients -->
                        <div class="absolute inset-0" style="background:radial-gradient(ellipse at 20% 30%, rgba(255,255,255,0.03), transparent 20%), radial-gradient(ellipse at 80% 70%, rgba(255,255,255,0.02), transparent 25%); mix-blend-mode:screen"></div>
                        <!-- floating droplets -->
                        <div class="absolute inset-0 pointer-events-none">
                            <span class="droplet" style="--i:0;left:12%;top:10%"></span>
                            <span class="droplet" style="--i:1;left:72%;top:20%"></span>
                            <span class="droplet" style="--i:2;left:36%;top:50%"></span>
                            <span class="droplet" style="--i:3;left:82%;top:64%"></span>
                            <span class="droplet" style="--i:4;left:22%;top:76%"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Droplet animation */
        .droplet{position:absolute;width:12px;height:12px;background:linear-gradient(180deg,rgba(255,255,255,0.9),rgba(255,255,255,0.25));border-radius:999px;box-shadow:0 6px 18px rgba(2,6,23,0.6);opacity:0.9;transform:translateY(0) scale(0.9);animation:drop 3.8s calc(var(--i)*0.4s) infinite cubic-bezier(.22,.9,.37,1)}
        @keyframes drop{0%{transform:translateY(0) scale(0.6);opacity:0}10%{opacity:1;transform:translateY(-8px) scale(1)}60%{transform:translateY(6px) scale(0.95)}100%{transform:translateY(0) scale(0.9);opacity:0}}
    </style>
</section>

<!-- Tickets area: keep unified cyan palette and realistic water-ish cards -->
<section id="tickets" class="mt-12">
    <h2 class="text-3xl font-bold text-cyan-100">Tickets</h2>
    <p class="text-sky-200/70 mt-2">Choose your vibe. Prices shown in NGN. Secure payment via integrated provider.</p>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($tickets as $ticket)
            <div class="relative p-0 rounded-3xl overflow-hidden transform hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-b from-cyan-900/40 via-cyan-800/20 to-slate-900/40"></div>
                <div class="relative z-10 p-6 bg-[linear-gradient(180deg,rgba(255,255,255,0.02),transparent)] border border-slate-800 rounded-3xl shadow-lg">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-cyan-700/30 flex items-center justify-center text-cyan-100 font-bold">{{ strtoupper(substr($ticket->name,0,1)) }}</div>
                                <div>
                                    <div class="text-lg font-extrabold text-cyan-100 leading-tight">{{ $ticket->name }}</div>
                                    <div class="text-sm text-sky-200 mt-1">{{ ucfirst(str_replace('-', ' ', $ticket->slug)) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-end">
                            <div class="text-sm text-sky-200">Price</div>
                            <div class="text-2xl font-bold text-cyan-300">₦{{ $ticket->formatted_price }}</div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3">
                        <div class="flex items-center gap-3">
                            @if($ticket->slug === 'early-bird')
                                <span class="px-2 py-1 rounded bg-emerald-500 text-slate-900 text-xs font-semibold">Early Bird</span>
                            @elseif($ticket->slug === 'vip')
                                <span class="px-2 py-1 rounded bg-amber-400 text-slate-900 text-xs font-semibold">VIP</span>
                            @else
                                <span class="px-2 py-1 rounded bg-sky-500 text-slate-900 text-xs font-semibold">Regular</span>
                            @endif
                            <span class="text-sm text-sky-200">Instant entry • Free water wristband</span>
                        </div>

                        <div class="text-sm text-sky-300">Experience: loud music, splash zones, beach vibes. Bring a towel and good mood.</div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        @auth
                            <form method="POST" action="{{ route('purchase.offline', $ticket->slug) }}">
                                @csrf
                                <input type="hidden" name="name" value="{{ auth()->user()->name }}" />
                                <input type="hidden" name="email" value="{{ auth()->user()->email }}" />
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold shadow-lg hover:scale-105 transform transition">Grab it</button>
                            </form>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-cyan-400 text-slate-900 font-semibold shadow-lg hover:scale-105 transform transition">Grab it</a>
                        @endauth
                        <div class="text-sm text-sky-300">Secure checkout • Fast</div>
                    </div>
                </div>

                <!-- water ripple footer accent -->
                <div class="mt-2 -mb-2 relative">
                    <svg viewBox="0 0 200 20" class="w-full h-6" preserveAspectRatio="none">
                        <path d="M0 10 Q 50 0 100 10 T 200 10 V20 H0z" fill="rgba(14,165,163,0.06)" />
                    </svg>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Modal removed: purchases submit directly for authenticated users; guests are redirected to register -->

@endsection
