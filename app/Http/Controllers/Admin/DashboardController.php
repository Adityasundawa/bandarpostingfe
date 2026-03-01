<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Admin Dashboard
     */
    public function index()
    {
        $stats = [
            'total_users'   => User::count(),
            'total_admins'  => User::where('role', User::ROLE_ADMIN)->count(),
            'total_clients' => User::where('role', User::ROLE_CLIENT)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

     public function blank()
    {
        $stats = [
            'total_users'   => User::count(),
            'total_admins'  => User::where('role', User::ROLE_ADMIN)->count(),
            'total_clients' => User::where('role', User::ROLE_CLIENT)->count(),
        ];

        return view('blank', compact('stats'));
    }
}
