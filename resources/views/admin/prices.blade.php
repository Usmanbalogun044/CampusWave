@extends('layouts.app')

@section('title','Admin — Ticket Prices')

@section('content')
    <div class="max-w-3xl mx-auto bg-slate-900 p-6 rounded-xl glass">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold">Ticket prices</h2>
                <p class="text-sm text-slate-300">Update displayed prices. Values entered as whole currency (e.g., 5000)</p>
            </div>
            <div>
                <a href="/admin" class="px-3 py-2 rounded bg-sky-600 text-slate-900 font-semibold">Dashboard</a>
            </div>
        </div>

        @if(session('success'))<div class="mt-4 p-3 rounded bg-emerald-600 text-slate-900 font-semibold">{{ session('success') }}</div>@endif

        <form method="POST" action="{{ route('admin.prices.update') }}" class="mt-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-3">
                @foreach($tickets as $t)
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="font-semibold">{{ $t->name }} <span class="text-sm text-slate-400">({{ $t->slug }})</span></div>
                        </div>
                        <div class="w-40">
                            <input type="number" name="prices[{{ $t->slug }}]" value="{{ number_format($t->price / 100, 0, '.', '') }}" class="w-full rounded bg-slate-800 border border-slate-700 px-3 py-2" />
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 text-right"><button class="px-4 py-2 rounded bg-emerald-400 text-slate-900 font-semibold">Save prices</button></div>
        </form>

        <p class="mt-4 text-sm text-slate-300">Note: This area is protected by admin middleware.</p>
    </div>
@endsection
