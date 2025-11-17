<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // ensure basic ticket types exist
        $defaults = [
            ['name' => 'Early Bird', 'slug' => 'early-bird', 'price' => 3000 * 100],
            ['name' => 'Regular', 'slug' => 'regular', 'price' => 5000 * 100],
            ['name' => 'VIP', 'slug' => 'vip', 'price' => 10000 * 100],
        ];

        foreach ($defaults as $d) {
            Ticket::firstOrCreate(['slug' => $d['slug']], $d);
        }

        $tickets = Ticket::orderBy('id')->get();

        return view('landing', compact('tickets'));
    }
}
