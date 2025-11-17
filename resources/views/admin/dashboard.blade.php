@extends('layouts.app')

@section('title','Admin Dashboard')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <div class="text-sm text-slate-300">Overview of tickets and purchases</div>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/prices" class="px-3 py-2 rounded bg-sky-600 text-slate-900 font-semibold">Edit prices</a>
            <a href="/" class="px-3 py-2 rounded border border-slate-700">Landing</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-slate-900 p-4 rounded-xl glass">
            <h2 class="font-semibold mb-2">Tickets</h2>
            <div class="space-y-3">
                @foreach($tickets as $t)
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $t->name }}</div>
                            <div class="text-sm text-slate-400">{{ $t->slug }}</div>
                        </div>
                        <div class="text-cyan-300 font-semibold">₦{{ $t->formatted_price }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-slate-900 p-4 rounded-xl glass">
            <h2 class="font-semibold mb-2">Recent Purchases</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-slate-400 text-left">
                            <th class="py-2">Ref</th>
                            <th class="py-2">User</th>
                            <th class="py-2">Phone</th>
                            <th class="py-2">Ticket</th>
                            <th class="py-2">Amount</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Date</th>
                            <th class="py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($purchases as $p)
                            <tr>
                                <td class="py-3">{{ $p->reference }}</td>
                                <td class="py-3">{{ optional($p->user)->email ?? 'Guest' }}</td>
                                <td class="py-3">{{ optional($p->user)->phone ?? optional($p->payload)['receipt']['whatsapp'] ?? '—' }}</td>
                                <td class="py-3">{{ optional($p->ticket)->name ?? '—' }}</td>
                                <td class="py-3">₦{{ number_format($p->amount / 100, 2) }}</td>
                                <td class="py-3">@if($p->status==='paid')<span class="px-2 py-1 rounded bg-emerald-500 text-slate-900">Paid</span>@else<span class="px-2 py-1 rounded bg-amber-500">{{ ucfirst($p->status) }}</span>@endif</td>
                                <td class="py-3">{{ $p->created_at }}</td>
                                <td class="py-3">
                                    @if($p->status !== 'paid')
                                        <form method="POST" action="{{ route('admin.purchases.accept', $p->id) }}">@csrf<button class="px-3 py-1 rounded bg-emerald-400 text-slate-900">Accept</button></form>
                                    @else
                                        <span class="text-sm text-sky-200">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
