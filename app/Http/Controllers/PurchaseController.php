<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    // Create an offline purchase record and redirect to bank details page
    public function createOffline(Request $request, $slug)
    {
        $ticket = Ticket::where('slug', $slug)->firstOrFail();

        $reference = 'OFF-' . now()->timestamp . '-' . $ticket->id . '-' . (Auth::id() ?? 'guest');

        $purchase = Purchase::create([
            'user_id' => Auth::id(),
            'ticket_id' => $ticket->id,
            'reference' => $reference,
            'amount' => $ticket->price,
            'currency' => env('OPAY_CURRENCY', 'NGN'),
            'status' => 'pending',
        ]);

        return redirect()->route('pay.offline.show', $purchase->id);
    }

    // Show bank account details and form to submit receipt/WA number
    public function showOffline($id)
    {
        $purchase = Purchase::with('ticket','user')->findOrFail($id);

        // secure: only owner or admin can view
        if ($purchase->user_id && Auth::id() !== $purchase->user_id && ! optional(Auth::user())->is_admin) {
            abort(403);
        }

        $account = env('PAYMENT_ACCOUNT_NUMBER', '0123456789');
        $bank = env('PAYMENT_ACCOUNT_BANK', 'YourBank');

        return view('pay.offline', compact('purchase','account','bank'));
    }

    // Handle receipt submission: store payload and optionally call WA API
    public function submitReceipt(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        // simple auth/owner check
        if ($purchase->user_id && Auth::id() !== $purchase->user_id && ! optional(Auth::user())->is_admin) {
            abort(403);
        }

        $data = $request->validate([
            'transaction_reference' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:64',
        ]);

        $payload = $purchase->payload ?: [];
        $payload['receipt'] = [
            'transaction_reference' => $data['transaction_reference'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'submitted_at' => now()->toDateTimeString(),
        ];

        $purchase->payload = $payload;
        $purchase->status = 'awaiting_admin_verification';
        $purchase->save();

        // send whatsapp message if configured
        if (! empty($data['whatsapp']) && env('WHATSAPP_API_URL')) {
            try {
                $msg = "Receipt received for purchase {$purchase->reference}. TxRef: {$data['transaction_reference']}";
                Http::withHeaders(['Authorization' => 'Bearer '.env('WHATSAPP_API_KEY')])
                    ->post(env('WHATSAPP_API_URL'), [
                        'to' => $data['whatsapp'],
                        'message' => $msg,
                    ]);
            } catch (\Exception $e) {
                // ignore send errors for now
            }
        }

        return redirect()->route('user.account')->with('success', 'Receipt submitted. Admin will verify and accept your payment.');
    }

    // Admin accepts purchase
    public function adminAccept(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->status = 'paid';
        $purchase->save();

        
        $wa = optional($purchase->payload)['receipt']['whatsapp'] ?? optional($purchase->user)->phone ?? null;
        // normalize to international digits-only format (very small heuristic)
        $wa_norm = $this->normalizePhone($wa);
        if ($wa_norm && env('WHATSAPP_API_URL')) {
            try {
                $buyerName = optional($purchase->user)->name ?? ($purchase->payload['receipt']['name'] ?? 'Customer');
                $ticketName = optional($purchase->ticket)->name ?? 'ticket';
                // amount stored in smallest unit (kobo/cents) — convert to major unit
                $amount = number_format(($purchase->amount ?? 0) / 100, 2);
                $currency = $purchase->currency ?? env('OPAY_CURRENCY', 'NGN');
                $adminContact = env('ADMIN_WHATSAPP_NUMBER');

                $msg = "Hi {$buyerName},\n\n" .
                    "Thank you — your booking for Campus Wave is confirmed.\n\n" .
                    "Ticket: {$ticketName}\n" .
                    "Amount: {$currency} {$amount}\n" .
                    "Reference: {$purchase->reference}\n\n" .
                    "Show this message at entry. If you need help, contact admin: {$adminContact}.\n\n" .
                    "See you at the event!\n" .
                    "— Campus Wave";
                $response = Http::withHeaders(['Authorization' => 'Bearer '.env('WHATSAPP_API_KEY')])
                    ->post(env('WHATSAPP_API_URL'), ['to' => $wa_norm, 'message' => $msg]);

                // Log result for debugging
                if ($response->successful()) {
                    Log::info('WA notify sent', ['purchase_id' => $purchase->id, 'to' => $wa_norm, 'resp' => $response->json()]);
                    $payload = $purchase->payload ?: [];
                    $payload['notified'] = ['ok' => true, 'to' => $wa_norm, 'raw_to' => $wa, 'resp' => $response->json(), 'at' => now()->toDateTimeString()];
                    $purchase->payload = $payload;
                    $purchase->save();
                } else {
                    Log::error('WA notify failed', ['purchase_id' => $purchase->id, 'to' => $wa_norm, 'status' => $response->status(), 'body' => $response->body()]);
                    $payload = $purchase->payload ?: [];
                    $payload['notified'] = ['ok' => false, 'to' => $wa_norm, 'raw_to' => $wa, 'status' => $response->status(), 'body' => $response->body(), 'at' => now()->toDateTimeString()];
                    $purchase->payload = $payload;
                    $purchase->save();
                }
            } catch (\Exception $e) {
                Log::error('WA notify exception', ['purchase_id' => $purchase->id, 'err' => $e->getMessage()]);
                $payload = $purchase->payload ?: [];
                $payload['notified'] = ['ok' => false, 'error' => $e->getMessage(), 'raw_to' => $wa, 'to' => $wa_norm, 'at' => now()->toDateTimeString()];
                $purchase->payload = $payload;
                $purchase->save();
            }
        }

        return back()->with('success', 'Purchase accepted and user notified');
    }

    // User account view
    public function account()
    {
        $user = Auth::user();
        $purchases = Purchase::with('ticket')->where('user_id', $user->id)->orderBy('created_at','desc')->get();
        return view('account', compact('purchases'));
    }

    // Normalize phone numbers for WhatsApp gateway (simple heuristic)
    private function normalizePhone($phone)
    {
        if (! $phone) return null;
        // strip spaces, dashes, parentheses but keep leading +
        $p = preg_replace('/[^0-9+]/', '', $phone);
        // remove leading + if present
        if (strpos($p, '+') === 0) $p = substr($p, 1);
        // if starts with 0, convert to 234 (common Nigerian format)
        if (preg_match('/^0[0-9]{9,}$/', $p)) {
            $p = preg_replace('/^0/', '234', $p);
        }
        return $p;
    }
}
