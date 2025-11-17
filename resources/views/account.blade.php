@extends('layouts.app')

@section('title','My Tickets — Campus Wave')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-cyan-100">My Tickets & Purchases</h2>
        <p class="text-sky-200 mt-2">All your purchases and ticket statuses appear here.</p>

        <div class="mt-6 space-y-4">
            @foreach($purchases as $p)
                <div class="bg-slate-900 p-4 rounded-lg glass border border-slate-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-semibold text-cyan-100">{{ optional($p->ticket)->name ?? '—' }}</div>
                            <div class="text-sm text-sky-300">Reference: {{ $p->reference }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-sky-200">Amount</div>
                            <div class="font-bold text-cyan-300">₦{{ number_format($p->amount / 100, 2) }}</div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <div class="text-sm text-sky-300">Status: <span class="font-semibold">{{ ucfirst($p->status) }}</span></div>
                        <div class="text-right">
                            @if(optional($p->payload)['receipt'] ?? false)
                                <div class="text-sm text-sky-200">Submitted Tx: {{ $p->payload['receipt']['transaction_reference'] ?? '—' }}</div>
                                <div class="text-sm text-sky-200">WA: {{ $p->payload['receipt']['whatsapp'] ?? '—' }}</div>
                            @endif
                            @php $adminWa = env('ADMIN_WHATSAPP_NUMBER'); @endphp
                            @if($adminWa && $p->status !== 'paid')
                                @php
                                    $msg = "Hello, I have transferred ₦".number_format($p->amount/100,2) ." for Campus Wave ({$p->ticket->name}). Reference: {$p->reference}. Account: " . env('PAYMENT_ACCOUNT_NUMBER') . " (" . env('PAYMENT_ACCOUNT_BANK') . "). I will attach the transfer screenshot.";
                                    $waLink = 'https://wa.me/' . $adminWa . '?text=' . rawurlencode($msg);
                                @endphp
                                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-2 px-3 py-2 rounded bg-emerald-400 text-slate-900 text-sm">Message Admin on WhatsApp</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
