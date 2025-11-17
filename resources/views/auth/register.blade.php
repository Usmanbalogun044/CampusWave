@extends('layouts.app')

@section('title','Create account — Campus Wave')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <!-- Left: Water splash visual -->
        <div class="hidden lg:flex items-center justify-center">
            <div class="relative w-full h-[520px] rounded-2xl overflow-hidden shadow-2xl">
                <img src="https://images.unsplash.com/photo-1503264116251-35a269479413?q=80&w=1200&auto=format&fit=crop&s=9d3c4f2a3738e1b1b5f3b9b3f0a3fbd0" alt="water" class="w-full h-full object-cover brightness-75">
                <div class="absolute inset-0 bg-gradient-to-t from-cyan-900/40 via-sky-900/20 to-transparent"></div>
                <div class="absolute left-8 bottom-8 bg-cyan-400/10 border border-cyan-300/20 text-cyan-100 px-4 py-3 rounded-lg backdrop-blur">
                    <div class="text-lg font-bold">Campus Wave</div>
                    <div class="text-sm text-cyan-200/80">Splash party • Good vibes • Fast pay</div>
                </div>
            </div>
        </div>

        <!-- Right: Form -->
        <div class="bg-slate-900 p-8 rounded-2xl glass shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold text-cyan-100">Create your account</h2>
                    <p class="mt-1 text-sky-200/80">Join the party — grab tickets faster with your account.</p>
                </div>
            </div>

            <form method="POST" action="{{ url('/register') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-sky-200">Full name</label>
                    <input name="name" required value="{{ old('name') }}" class="mt-2 block w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="Your full name">
                    @error('name') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm text-sky-200">Email address</label>
                    <input name="email" type="email" required value="{{ old('email') }}" class="mt-2 block w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="you@example.com">
                    @error('email') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm text-sky-200">WhatsApp number</label>
                    <input name="phone" type="text" required value="{{ old('phone') }}" class="mt-2 block w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="Country code + number, e.g. 2348012345678">
                    <div class="text-xs text-sky-300 mt-1">Please provide your WhatsApp number (include country code, no plus sign).</div>
                    @error('phone') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-sky-200">Password</label>
                        <input name="password" type="password" required class="mt-2 block w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="At least 6 characters">
                        @error('password') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-sky-200">Confirm password</label>
                        <input name="password_confirmation" type="password" required class="mt-2 block w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="Repeat password">
                    </div>
                </div>

                <div class="flex items-center justify-between mt-2">
                    <div class="text-sm text-sky-200">By creating an account you agree to our <a href="#" class="text-cyan-300 underline">terms</a>.</div>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-cyan-400 text-slate-900 font-semibold shadow-lg">Create account</button>
                </div>
            </form>

            <div class="mt-6 text-center text-sky-200">Already have an account? <a href="{{ route('login') }}" class="text-cyan-300 font-semibold">Sign in</a></div>
        </div>
    </div>
</div>

@endsection
