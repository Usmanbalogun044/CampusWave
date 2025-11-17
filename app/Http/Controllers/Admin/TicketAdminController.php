<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketAdminController extends Controller
{
    // NOTE: You should add auth + admin middleware to protect these routes.

    public function index()
    {
        $tickets = Ticket::orderBy('id')->get();
        return view('admin.prices', compact('tickets'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);

        foreach ($data['prices'] as $slug => $value) {
            $ticket = Ticket::where('slug', $slug)->first();
            if ($ticket) {
                // accept input in whole currency units (e.g., 5000) and store cents
                $ticket->price = intval(floatval($value) * 100);
                $ticket->save();
            }
        }

        return back()->with('success', 'Prices updated');
    }
}
