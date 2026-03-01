<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TiktokPanelController extends Controller
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
