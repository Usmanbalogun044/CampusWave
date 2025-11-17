<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TicketPaymentController extends Controller
{
    public function create(Request $request, $slug)
    {
        $ticket = Ticket::where('slug', $slug)->firstOrFail();

        // Build payment payload — configurable via .env
        $payload = [
            'merchant_id' => env('OPAY_MERCHANT_ID'),
            'amount' => $ticket->price, // expected in kobo/cents by our convention
            'currency' => env('OPAY_CURRENCY', 'NGN'),
            'reference' => 'CW-' . now()->timestamp . '-' . $ticket->id,
            'callback_url' => route('opay.callback'),
            'customer' => [
                'name' => $request->input('name', 'Guest'),
                'email' => $request->input('email', null),
            ],
            'metadata' => [
                'ticket' => $ticket->slug,
            ],
        ];
        $opayUrl = env('OPAY_API_URL');
        if (! $opayUrl) {
            return back()->with('error', 'Payment not configured. OPAY_API_URL missing.');
        }
        // create a purchase record before calling the payment provider
        $purchase = Purchase::create([
            'user_id' => Auth::id(),
            'ticket_id' => $ticket->id,
            'reference' => $payload['reference'],
            'amount' => $ticket->price,
            'currency' => $payload['currency'],
            'status' => 'pending',
            'payload' => null,
        ]);

        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPAY_SECRET'),
                'Accept' => 'application/json',
            ])->post(rtrim($opayUrl, '/') . '/payments', $payload);

            if ($res->successful()) {
                $data = $res->json();
                // assume response contains `payment_url`
                if (! empty($data['payment_url'])) {
                    return redirect()->away($data['payment_url']);
                }
                return back()->with('error', 'Payment created but no redirect URL returned.');
            }

            return back()->with('error', 'Payment provider error: ' . $res->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $data = $request->all();

        // Attempt to find purchase by reference
        $reference = $data['reference'] ?? $data['tx_ref'] ?? null;
        if ($reference) {
            $purchase = Purchase::where('reference', $reference)->first();
            if ($purchase) {
                // naive status mapping — adapt to provider fields
                $status = $data['status'] ?? ($data['success'] ? 'paid' : 'failed');
                if ($status === 'paid' || $status === 'success') {
                    $purchase->status = 'paid';
                } else {
                    $purchase->status = 'failed';
                }
                $purchase->payload = $data;
                $purchase->save();
            }
        }

        return view('opay.callback', ['payload' => $data]);
    }
}
