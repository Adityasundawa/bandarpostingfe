<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Client Area
     */
    public function index()
    {
        $user = auth()->user();

        return view('client.dashboard', compact('user'));
    }
}
