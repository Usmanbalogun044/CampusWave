@extends('layouts.app')

@section('title','Bank transfer — Campus Wave')

@section('content')
    <div class="max-w-3xl mx-auto bg-slate-900 p-6 rounded-2xl glass shadow">
        <h2 class="text-2xl font-bold text-cyan-100">Bank transfer details</h2>
        <p class="text-sky-200 mt-2">Send the ticket amount to the account below. After transfer, submit your transaction reference and WhatsApp number so we can confirm.</p>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 rounded-lg bg-gradient-to-b from-cyan-900/10 to-transparent border border-cyan-800">
                <div class="text-sm text-sky-200">Account name</div>
                <div class="font-semibold text-cyan-100 text-lg">{{ env('PAYMENT_ACCOUNT_NAME', 'Campus Wave') }}</div>

                <div class="mt-3 text-sm text-sky-200">Bank</div>
                <div class="font-semibold text-cyan-100">{{ $bank }}</div>

                <div class="mt-3 text-sm text-sky-200">Account number</div>
                <div class="font-semibold text-cyan-100 text-2xl">{{ $account }}</div>

                <div class="mt-4 text-sm text-sky-200">Amount</div>
                <div class="font-bold text-cyan-300 text-xl">₦{{ number_format($purchase->amount / 100, 2) }}</div>
            </div>

            <div class="p-6 rounded-lg bg-slate-800/40 border border-slate-700">
                <h3 class="text-lg font-semibold text-cyan-100">Quick-send receipt to admin</h3>
                <p class="text-sky-200 mt-2">For fastest verification, send your transfer screenshot to the admin on WhatsApp. Tap the button below to open WhatsApp with a prefilled message.</p>

                @php $adminWa = env('ADMIN_WHATSAPP_NUMBER'); @endphp
                @if($adminWa)
                    @php
                        $msg = "Hello, I have transferred ₦".number_format($purchase->amount/100,2) ." for Campus Wave ({$purchase->ticket->name}). Reference: {$purchase->reference}. Account: {$account} ({$bank}). I will attach the transfer screenshot.";
                        $waLink = 'https://wa.me/' . $adminWa . '?text=' . rawurlencode($msg);
                    @endphp

                    <div class="mt-4 flex gap-3 items-center">
                        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-emerald-400 text-slate-900 font-semibold">Message Admin on WhatsApp</a>
                        <a href="{{ route('user.account') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded border border-slate-700 text-sky-200">Back to my account</a>
                    </div>

                    <div class="mt-2 text-sm text-sky-300">After sending, return to your account to see status updates; admin will accept once verified.</div>
                @else
                    <div class="mt-4 text-sm text-rose-400">Admin WhatsApp number not configured. Please submit your receipt details from your account page.</div>
                    <div class="mt-3"><a href="{{ route('user.account') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded border border-slate-700 text-sky-200">Back to my account</a></div>
                @endif
            </div>
        </div>

        <div class="mt-6 text-sm text-sky-300">After you submit, admin will verify your payment and accept your ticket. You'll receive a WhatsApp message when approved (if number provided).</div>
    </div>
@endsection
