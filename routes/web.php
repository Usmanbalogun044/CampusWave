<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\TicketPaymentController;
use App\Http\Controllers\Admin\TicketAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Purchase;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PurchaseController;

Route::get('/', function () {
    return app(LandingController::class)->index();
});

// Payment routes
Route::post('/pay/{slug}', [TicketPaymentController::class, 'create'])->name('ticket.pay');
Route::post('/opay/callback', [TicketPaymentController::class, 'callback'])->name('opay.callback');

// Admin routes (not protected - add auth + middleware in production)
// Authentication routes (simple)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes (protected by middleware class)
Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('/admin/prices', [TicketAdminController::class, 'index'])->name('admin.prices');
    Route::post('/admin/prices', [TicketAdminController::class, 'update'])->name('admin.prices.update');
    // Admin dashboard and pages
    Route::get('/admin', function () {
        $tickets = Ticket::orderBy('id')->get();
        $purchases = Purchase::with('ticket','user')->orderBy('created_at','desc')->take(8)->get();
        return view('admin.dashboard', compact('tickets','purchases'));
    })->name('admin.dashboard');

    // Dedicated purchases page (full list)
    Route::get('/admin/purchases', function () {
        $purchases = Purchase::with('ticket','user')->orderBy('created_at','desc')->get();
        return view('admin.purchases', compact('purchases'));
    })->name('admin.purchases');

    // Admin users list
    Route::get('/admin/users', function () {
        $users = \App\Models\User::orderBy('id')->get();
        return view('admin.users', compact('users'));
    })->name('admin.users');
});

// Offline purchase flow: create purchase and show bank details
Route::post('/purchase/{slug}', [PurchaseController::class, 'createOffline'])->name('purchase.offline');
Route::get('/pay/offline/{id}', [PurchaseController::class, 'showOffline'])->name('pay.offline.show');
Route::post('/pay/offline/{id}/receipt', [PurchaseController::class, 'submitReceipt'])->name('pay.offline.receipt');

// Admin accept purchase
Route::post('/admin/purchases/{id}/accept', [PurchaseController::class, 'adminAccept'])->name('admin.purchases.accept')->middleware(AdminMiddleware::class);

// User account
Route::get('/account', [PurchaseController::class, 'account'])->name('user.account')->middleware('auth');

// Debug helper: show purchase details and computed WA target (only in local/debug)
Route::get('/debug/purchase/{id}', function ($id) {
    if (!env('APP_DEBUG')) abort(403);
    $p = \App\Models\Purchase::with('user','ticket')->find($id);
    if (! $p) return response()->json(['error' => 'not found'], 404);
    $wa = optional($p->payload)['receipt']['whatsapp'] ?? optional($p->user)->phone ?? null;
    return response()->json([
        'purchase' => $p,
        'computed_whatsapp' => $wa,
        'whatsapp_api_url' => env('WHATSAPP_API_URL'),
        'whatsapp_api_key_set' => (bool) env('WHATSAPP_API_KEY'),
    ]);
});
