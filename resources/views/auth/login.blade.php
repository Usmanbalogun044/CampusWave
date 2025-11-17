@extends('layouts.app')

@section('title','Sign in — Campus Wave')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <div class="hidden md:flex items-center justify-center">
            <div class="relative w-full h-[420px] rounded-2xl overflow-hidden shadow-2xl">
                <img src="https://images.unsplash.com/photo-1503264116251-35a269479413?q=80&w=1200&auto=format&fit=crop&s=9d3c4f2a3738e1b1b5f3b9b3f0a3fbd0" alt="splash" class="w-full h-full object-cover brightness-75">
                <div class="absolute inset-0 bg-gradient-to-t from-cyan-900/40 via-sky-900/20 to-transparent"></div>
                <div class="absolute left-6 bottom-6 bg-cyan-400/10 border border-cyan-300/20 text-cyan-100 px-4 py-3 rounded-lg backdrop-blur">
                    <div class="text-lg font-bold">Welcome back</div>
                    <div class="text-sm text-cyan-200/80">Sign in to grab your tickets</div>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 p-8 rounded-2xl glass shadow-lg">
            <h2 class="text-3xl font-extrabold text-cyan-100">Sign in</h2>
            <p class="mt-1 text-sky-200/80">Secure login to continue to Campus Wave.</p>

            <form method="POST" action="{{ url('/login') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-sky-200">Email</label>
                    <input name="email" type="email" required value="{{ old('email') }}" class="mt-2 block w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="you@example.com">
                    @error('email') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm text-sky-200">Password</label>
                    <input name="password" type="password" required class="mt-2 block w-full rounded-lg bg-slate-800 border border-slate-700 px-4 py-3 text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-400" placeholder="Your password">
                    @error('password') <div class="text-rose-400 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-sky-200"><a href="#" class="text-cyan-300">Forgot password?</a></div>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-cyan-400 text-slate-900 font-semibold shadow-lg">Sign in</button>
                </div>
            </form>

            <div class="mt-6 text-center text-sky-200">New here? <a href="{{ route('register') }}" class="text-cyan-300 font-semibold">Create an account</a></div>
        </div>
    </div>
</div>

@endsection
